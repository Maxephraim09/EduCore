<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PromotionController extends Controller
{
    public function index(Request $request)
    {
        $promotionEnabled = filter_var(SystemSetting::getValue('promotion_enabled', 'true'), FILTER_VALIDATE_BOOLEAN);
        $minAverageScore = (float) SystemSetting::getValue('promotion_min_average_score', 50);
        $maxAbsenteeism = (int) SystemSetting::getValue('promotion_max_absenteeism', 10);
        $minPassingSubjects = (int) SystemSetting::getValue('promotion_min_passing_subjects', 7);
        $coreSubjectsSetting = SystemSetting::getValue('promotion_core_subjects', 'English, Mathematics');
        $coreSubjects = $this->normalizeCoreSubjects($coreSubjectsSetting);
        $autoPromote = filter_var(SystemSetting::getValue('promotion_auto_promote', 'true'), FILTER_VALIDATE_BOOLEAN);

        $defaultAcademicYear = AcademicYear::getCurrent()?->name
            ?? AcademicYear::orderByDesc('start_date')->value('name')
            ?? date('Y') . '/' . (date('Y') + 1);
        $currentAcademicYear = $request->input('academic_year', SystemSetting::getValue('academic_year', $defaultAcademicYear));

        $defaultTerm = Term::getCurrent()?->name
            ?? Term::orderByDesc('sequence')->value('name')
            ?? 'Third Term';
        $currentTerm = $this->normalizeTerm(SystemSetting::getValue('term', $defaultTerm));
        $promotionWindowOpen = $currentTerm === 'Third Term';
        $selectedClassId = $request->input('class_id');

        $classes = ClassModel::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('section')
            ->get();

        $selectedClass = $selectedClassId
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null;

        if (!$selectedClass && !$classes->isEmpty()) {
            $selectedClass = $classes->first();
        }

        $promotionMappings = $this->getPromotionMappings();
        $reviewStudents = collect();

        if ($selectedClass) {
            $students = Student::query()
                ->where('is_active', true)
                ->where('class_id', $selectedClass->id)
                ->with(['class', 'results.subject', 'results.exam'])
                ->orderBy('first_name')
                ->get();

            $reviewStudents = $students->map(function (Student $student) use (
                $promotionEnabled,
                $minAverageScore,
                $maxAbsenteeism,
                $minPassingSubjects,
                $coreSubjects,
                $currentAcademicYear,
                $promotionWindowOpen,
                $promotionMappings
            ) {
                $results = $student->results->filter(function ($result) use ($currentAcademicYear) {
                    return optional($result->exam)->academic_year === $currentAcademicYear;
                });

                $termNames = $results->map(fn ($result) => $this->normalizeTerm(optional($result->exam)->term))->unique()->values()->all();
                $hasCompletedAllTerms = collect(['First Term', 'Second Term', 'Third Term'])->every(fn ($term) => in_array($term, $termNames));

                $scores = $results->map(fn ($result) => $this->getResultScore($result));
                $seasonalTotal = round($scores->sum() ?? 0, 2);
                $seasonalAverage = $results->isEmpty() ? 0 : round($seasonalTotal / $results->count(), 2);
                $passingSubjects = $results
                    ->filter(fn ($result) => $this->getResultScore($result) >= 40)
                    ->pluck('subject_id')
                    ->unique()
                    ->count();
                $absences = $this->getAbsenteeismCount($student, $currentAcademicYear);
                $promotionTarget = $this->resolvePromotionTarget($student, $promotionMappings);
                $targetClass = $promotionTarget && $promotionTarget['target_type'] === 'class' ? ClassModel::find($promotionTarget['target_class_id']) : null;
                $targetFullName = $promotionTarget && $promotionTarget['target_type'] === 'class' && $targetClass ? $targetClass->full_class_name : ($promotionTarget && $promotionTarget['target_type'] === 'alumni' ? 'Alumni' : 'Not assigned');
                $targetCapacity = $promotionTarget && $promotionTarget['target_type'] === 'class' && $targetClass ? $targetClass->capacity : null;
                $targetCurrentCount = $promotionTarget && $promotionTarget['target_type'] === 'class' && $targetClass ? $targetClass->students()->where('is_active', true)->count() : null;
                $targetHasCapacity = $promotionTarget === null ? false : ($promotionTarget['target_type'] === 'alumni' || ($promotionTarget['target_type'] === 'class' && (!$targetCapacity || $targetCurrentCount < $targetCapacity)));

                $coreSubjectFailures = $this->hasFailedCoreSubjects($results, $coreSubjects);
                $eligible = $promotionEnabled
                    && $promotionWindowOpen
                    && $hasCompletedAllTerms
                    && $seasonalAverage >= $minAverageScore
                    && $passingSubjects >= $minPassingSubjects
                    && !$coreSubjectFailures
                    && $absences <= $maxAbsenteeism
                    && $promotionTarget !== null
                    && $targetHasCapacity;

                $reasons = [];
                if (!$promotionEnabled) {
                    $reasons[] = 'Promotions are disabled by system settings.';
                }
                if (!$promotionWindowOpen) {
                    $reasons[] = 'Promotions are only available at the end of Third Term.';
                }
                if (!$hasCompletedAllTerms) {
                    $reasons[] = 'Student has not completed all three terms for ' . $currentAcademicYear . '.';
                }
                if ($seasonalAverage < $minAverageScore) {
                    $reasons[] = 'Average score is below required minimum.';
                }
                if ($passingSubjects < $minPassingSubjects) {
                    $reasons[] = 'Student has fewer passing subjects than required.';
                }
                if ($coreSubjectFailures) {
                    $reasons[] = 'Student failed one or more core subjects.';
                }
                if ($absences > $maxAbsenteeism) {
                    $reasons[] = 'Absenteeism exceeds allowed maximum.';
                }
                if ($promotionTarget === null) {
                    $reasons[] = 'No promotion mapping has been configured for this class.';
                }
                if (!$targetHasCapacity) {
                    $reasons[] = 'The configured target class is already full.';
                }
                if ($results->isEmpty()) {
                    $reasons[] = 'No exam results were found for the current academic year.';
                }

                return [
                    'student' => $student,
                    'current_class' => optional($student->class)->full_class_name ?? $student->class ?? 'N/A',
                    'target_class_name' => $targetFullName,
                    'seasonal_total' => $seasonalTotal,
                    'seasonal_average' => $seasonalAverage,
                    'passing_subjects' => $passingSubjects,
                    'absences' => $absences,
                    'completed_all_terms' => $hasCompletedAllTerms,
                    'eligible' => $eligible,
                    'status' => $eligible ? 'Eligible' : 'Pending',
                    'reasons' => $reasons,
                ];
            })->values();

            $rankedStudents = $reviewStudents->sortByDesc('seasonal_average')->values();
            $position = 1;
            $lastAverage = null;
            $rankedStudents->transform(function ($entry) use (&$position, &$lastAverage) {
                if ($lastAverage !== null && $entry['seasonal_average'] < $lastAverage) {
                    $position = $position + 1;
                }
                $entry['class_position'] = $position;
                $lastAverage = $entry['seasonal_average'];
                return $entry;
            });
            $reviewStudents = $rankedStudents->sortBy('student.first_name')->values();
        }

        return view('promotions.index', compact(
            'reviewStudents',
            'promotionEnabled',
            'promotionWindowOpen',
            'minAverageScore',
            'maxAbsenteeism',
            'minPassingSubjects',
            'coreSubjects',
            'autoPromote',
            'currentAcademicYear',
            'currentTerm',
            'classes',
            'selectedClass',
            'promotionMappings'
        ));
    }

    public function settings()
    {
        $classes = ClassModel::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('section')
            ->get();
        $promotionMappings = $this->getPromotionMappings();

        return view('promotions.settings', compact('classes', 'promotionMappings'));
    }

    public function storeSettings(Request $request)
    {
        $request->validate([
            'source_class_id' => ['required', 'exists:classes,id'],
            'target_type' => ['required', 'in:class,alumni'],
            'target_class_id' => ['nullable', 'exists:classes,id'],
        ]);

        $sourceClass = ClassModel::findOrFail($request->input('source_class_id'));
        $targetType = $request->input('target_type');
        $targetClassId = $request->input('target_class_id');
        $targetClass = $targetClassId ? ClassModel::find($targetClassId) : null;

        if ($targetType === 'class' && !$targetClass) {
            return back()->with('error', 'Please select a target class for class-based promotion.');
        }

        $mappings = $this->getPromotionMappings();
        $key = $sourceClass->id . ':' . $targetType . ':' . ($targetClass ? $targetClass->id : 0);
        $mappings[$key] = [
            'source_class_id' => $sourceClass->id,
            'source_class_name' => $sourceClass->full_class_name,
            'target_type' => $targetType,
            'target_class_id' => $targetClass ? $targetClass->id : null,
            'target_class_name' => $targetClass ? $targetClass->full_class_name : null,
        ];

        SystemSetting::setValue('promotion_mappings', json_encode(array_values($mappings)), 'text', 'Promotion class mappings');

        return back()->with('success', 'Promotion mapping saved successfully.');
    }

    public function removeMapping(Request $request)
    {
        $request->validate(['mapping_key' => ['required', 'string']]);

        $mappings = $this->getPromotionMappings();
        unset($mappings[$request->input('mapping_key')]);
        SystemSetting::setValue('promotion_mappings', json_encode(array_values($mappings)), 'text', 'Promotion class mappings');

        return back()->with('success', 'Promotion mapping removed successfully.');
    }

    public function promote(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        if (empty($studentIds)) {
            return back()->with('error', 'No students selected for promotion.');
        }

        $students = Student::whereIn('id', $studentIds)->with('class')->get();
        $promotedCount = 0;
        $skippedCount = 0;
        $mappings = $this->getPromotionMappings();

        foreach ($students as $student) {
            $promotionTarget = $this->resolvePromotionTarget($student, $mappings);
            if (!$promotionTarget) {
                $skippedCount++;
                continue;
            }

            if ($promotionTarget['target_type'] === 'class') {
                $targetClass = ClassModel::find($promotionTarget['target_class_id']);
                if (!$targetClass) {
                    $skippedCount++;
                    continue;
                }

                $targetCapacity = $targetClass->capacity;
                $currentTargetCount = $targetClass->students()->where('is_active', true)->count();
                if ($targetCapacity && $currentTargetCount >= $targetCapacity) {
                    $skippedCount++;
                    continue;
                }

                $nextAcademicYear = $this->getNextAcademicYear($student->academic_year ?? SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1)));
                $student->update([
                    'class_id' => $targetClass->id,
                    'class' => $targetClass->full_class_name,
                    'academic_year' => $nextAcademicYear,
                    'is_active' => true,
                    'is_alumni' => false,
                ]);
                $promotedCount++;
                continue;
            }

            $student->update([
                'class_id' => null,
                'class' => 'Alumni',
                'academic_year' => $student->academic_year ?? SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1)),
                'is_active' => false,
                'is_alumni' => true,
            ]);
            $promotedCount++;
        }

        $message = "Promotion completed for {$promotedCount} student(s).";
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} student(s) were skipped because a valid promotion target was not available.";
        }

        return back()->with('success', $message);
    }

    private function getPromotionMappings(): array
    {
        $rawMappings = SystemSetting::getValue('promotion_mappings', '[]');
        if (empty($rawMappings)) {
            return [];
        }

        $decoded = json_decode((string) $rawMappings, true);
        if (!is_array($decoded)) {
            return [];
        }

        $mappings = [];
        foreach ($decoded as $mapping) {
            if (!is_array($mapping) || empty($mapping['source_class_id'])) {
                continue;
            }
            $mappings[(int) $mapping['source_class_id']] = $mapping;
        }

        return $mappings;
    }

    private function resolvePromotionTarget(Student $student, array $mappings): ?array
    {
        if (!$student->class_id) {
            return null;
        }

        $currentClass = $student->class()->first();
        if ($currentClass && isset($mappings[(int) $currentClass->id])) {
            return $mappings[(int) $currentClass->id];
        }

        if (!$currentClass) {
            return null;
        }

        $targetClass = $this->findNextTargetClass($currentClass);
        if (!$targetClass) {
            return null;
        }

        return [
            'source_class_id' => $currentClass->id,
            'source_class_name' => $currentClass->full_class_name,
            'target_type' => 'class',
            'target_class_id' => $targetClass->id,
            'target_class_name' => $targetClass->full_class_name,
        ];
    }

    private function findNextTargetClass(ClassModel $sourceClass): ?ClassModel
    {
        $academicYear = $sourceClass->academic_year ?? SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1));
        $sourceName = trim((string) ($sourceClass->name ?? $sourceClass->full_name ?? $sourceClass->full_class_name ?? ''));
        $normalized = preg_replace('/\s+/', ' ', strtoupper($sourceName));

        if (!preg_match('/^(JSS|SSS)\s*([1-3])(?:\s+([A-Z]))?$/i', $normalized, $matches)) {
            return null;
        }

        $prefix = strtoupper($matches[1]);
        $level = (int) $matches[2];
        $section = isset($matches[3]) ? trim($matches[3]) : null;
        $nextLevel = null;

        if ($prefix === 'JSS') {
            if ($level === 1) {
                $nextLevel = 'JSS 2';
            } elseif ($level === 2) {
                $nextLevel = 'JSS 3';
            } elseif ($level === 3) {
                $nextLevel = 'SSS 1';
            }
        } elseif ($prefix === 'SSS') {
            if ($level === 1) {
                $nextLevel = 'SSS 2';
            } elseif ($level === 2) {
                $nextLevel = 'SSS 3';
            }
        }

        if (!$nextLevel) {
            return null;
        }

        $query = ClassModel::query()
            ->where('is_active', true)
            ->where(function ($query) use ($nextLevel) {
                $query->where('name', $nextLevel)
                    ->orWhere('full_name', $nextLevel);
            });

        if (!empty($academicYear)) {
            $query->where(function ($query) use ($academicYear) {
                $query->where('academic_year', $academicYear)
                    ->orWhereNull('academic_year');
            });
        }

        $candidates = $query->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        $preferredSection = strtoupper((string) $section);
        $sameSection = $candidates->first(function ($candidate) use ($preferredSection) {
            return $preferredSection && strtoupper((string) ($candidate->section ?? '')) === $preferredSection && $this->classHasCapacity($candidate);
        });
        if ($sameSection) {
            return $sameSection;
        }

        $fallbackSection = $candidates->first(function ($candidate) {
            return $this->classHasCapacity($candidate);
        });
        if ($fallbackSection) {
            return $fallbackSection;
        }

        return $candidates->first();
    }

    private function classHasCapacity(ClassModel $class): bool
    {
        $capacity = (int) $class->capacity;
        if ($capacity <= 0) {
            return true;
        }

        return $class->students()->where('is_active', true)->count() < $capacity;
    }

    private function normalizeTerm($term)
    {
        $term = trim((string) $term);
        $lower = strtolower($term);

        if (str_contains($lower, '1st') || str_contains($lower, 'first')) {
            return 'First Term';
        }
        if (str_contains($lower, '2nd') || str_contains($lower, 'second')) {
            return 'Second Term';
        }
        if (str_contains($lower, '3rd') || str_contains($lower, 'third')) {
            return 'Third Term';
        }

        return ucwords($term);
    }

    private function getResultScore($result)
    {
        return (float) (($result->total_score ?? $result->score ?? 0));
    }

    private function normalizeCoreSubjects($coreSubjects): array
    {
        if (is_array($coreSubjects)) {
            return array_values(array_filter(array_map('trim', $coreSubjects)));
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $coreSubjects))));
    }

    private function hasFailedCoreSubjects($results, array $coreSubjects)
    {
        foreach ($coreSubjects as $subjectName) {
            $subjectResult = $results->first(function ($result) use ($subjectName) {
                $subject = $result->subject;
                return $subject && strcasecmp((string) $subject->name, $subjectName) === 0;
            });

            if (!$subjectResult || $this->getResultScore($subjectResult) < 40) {
                return true;
            }
        }

        return false;
    }

    private function getAbsenteeismCount(Student $student, string $academicYear)
    {
        if (!Schema::hasTable('attendances')) {
            return 0;
        }

        try {
            $query = DB::table('attendances')
                ->where('student_id', $student->id)
                ->where('status', 'absent');

            if (Schema::hasColumn('attendances', 'academic_year')) {
                $query->where('academic_year', $academicYear);
            }

            return $query->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function getNextClassForStudent(Student $student)
    {
        $classModel = $student->class;
        if (!$classModel) {
            return null;
        }

        $currentName = trim((string) ($classModel->name ?? $classModel->full_class_name ?? ''));
        if (!$currentName) {
            return null;
        }

        $normalized = preg_replace('/\s+/', ' ', strtoupper($currentName));
        if (!preg_match('/^(JSS|SSS)\s*([1-3])(?:\s+([A-Z]))?$/i', $normalized, $matches)) {
            return null;
        }

        $prefix = strtoupper($matches[1]);
        $level = (int) $matches[2];
        $section = isset($matches[3]) ? trim($matches[3]) : null;
        $nextLevel = null;

        if ($prefix === 'JSS') {
            if ($level === 1) {
                $nextLevel = 'JSS 2';
            } elseif ($level === 2) {
                $nextLevel = 'JSS 3';
            } elseif ($level === 3) {
                $nextLevel = 'SSS 1';
            }
        } elseif ($prefix === 'SSS') {
            if ($level === 1) {
                $nextLevel = 'SSS 2';
            } elseif ($level === 2) {
                $nextLevel = 'SSS 3';
            } else {
                return null;
            }
        }

        if (!$nextLevel) {
            return null;
        }

        $searchFullName = $section ? $nextLevel . ' ' . $section : $nextLevel;
        $candidate = ClassModel::where('full_name', $searchFullName)->first();

        if (!$candidate) {
            $query = ClassModel::where('name', $nextLevel)->where('is_active', true);
            if ($section) {
                $query->where(function ($query) use ($section, $nextLevel) {
                    $query->where('section', $section)
                          ->orWhere('full_name', $nextLevel . ' ' . $section);
                });
            }
            $candidate = $query->orderBy('section')->first();
        }

        if (!$candidate) {
            $candidate = ClassModel::where('name', $nextLevel)->where('is_active', true)->first();
        }

        return $candidate;
    }

    private function getNextAcademicYear(string $academicYear)
    {
        if (preg_match('/^(\d{4})\s*\/\s*(\d{4})$/', trim($academicYear), $matches)) {
            return ($matches[1] + 1) . '/' . ($matches[2] + 1);
        }

        return $academicYear;
    }
}
