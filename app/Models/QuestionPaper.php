<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPaper extends Model
{
    use HasFactory;

    protected $table = 'question_papers';

    protected $fillable = [
        'academic_year',
        'term',
        'class_id',
        'subject_id',
        'teacher_id',
        'created_by',
        'title',
        'code',
        'instructions',
        'description',
        'time_allowed',
        'total_marks',
        'passing_marks',
        'start_date',
        'end_date',
        'is_published',
        'published_at',
        'shuffle_questions',
        'show_results_immediately',
        'allow_review',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'shuffle_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_review' => 'boolean',
        'time_allowed' => 'integer',
        'total_marks' => 'integer',
        'passing_marks' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================
    
    /**
     * Get the subject that this question paper belongs to
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class that this question paper belongs to
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    /**
     * Get the teacher who created this question paper
     */
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    /**
     * Get the user who created this question paper
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all questions for this question paper
     */
    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Get the full title with subject and term
     */
    public function getFullTitleAttribute()
    {
        return $this->subject->name . ' - ' . $this->term . ' ' . $this->academic_year;
    }

    /**
     * Get the total marks for this question paper
     */
    public function getTotalMarksAttribute()
    {
        return $this->questions()->sum('marks');
    }

    /**
     * Get the total number of questions
     */
    public function getQuestionCountAttribute()
    {
        return $this->questions()->count();
    }

    /**
     * Get the status label
     */
    public function getStatusAttribute()
    {
        return $this->is_published ? 'Published' : 'Draft';
    }

    /**
     * Get the status color for badges
     */
    public function getStatusColorAttribute()
    {
        return $this->is_published ? 'success' : 'warning';
    }

    /**
     * Get the formatted time allowed
     */
    public function getTimeAllowedFormattedAttribute()
    {
        if (!$this->time_allowed) return 'N/A';
        
        $hours = floor($this->time_allowed / 60);
        $minutes = $this->time_allowed % 60;
        
        if ($hours > 0 && $minutes > 0) {
            return $hours . 'h ' . $minutes . 'm';
        } elseif ($hours > 0) {
            return $hours . ' hour' . ($hours > 1 ? 's' : '');
        } else {
            return $minutes . ' minutes';
        }
    }

    /**
     * Check if the question paper is published
     */
    public function getIsPublishedAttribute($value)
    {
        return (bool) $value;
    }

    // ==================== SCOPES ====================
    
    /**
     * Scope a query to only include published question papers
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include draft question papers
     */
    public function scopeDraft($query)
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope a query to filter by class
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to filter by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope a query to filter by term
     */
    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope a query to filter by academic year
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope a query to search by title or code
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
              ->orWhere('code', 'LIKE', "%{$search}%");
        });
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Check if the question paper has questions
     */
    public function hasQuestions()
    {
        return $this->questions()->count() > 0;
    }

    /**
     * Get the question types distribution
     */
    public function getQuestionTypeDistribution()
    {
        return $this->questions()
            ->select('type', \DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    /**
     * Get questions by type
     */
    public function getQuestionsByType($type)
    {
        return $this->questions()->where('type', $type)->get();
    }

    /**
     * Get multiple choice questions
     */
    public function getMultipleChoiceQuestions()
    {
        return $this->getQuestionsByType('multiple_choice');
    }

    /**
     * Get true/false questions
     */
    public function getTrueFalseQuestions()
    {
        return $this->getQuestionsByType('true_false');
    }

    /**
     * Get short answer questions
     */
    public function getShortAnswerQuestions()
    {
        return $this->getQuestionsByType('short_answer');
    }

    /**
     * Get essay questions
     */
    public function getEssayQuestions()
    {
        return $this->getQuestionsByType('essay');
    }

    /**
     * Get question summary
     */
    public function getQuestionSummary()
    {
        $distribution = $this->getQuestionTypeDistribution();
        
        return [
            'total' => $this->question_count,
            'total_marks' => $this->total_marks,
            'multiple_choice' => $distribution['multiple_choice'] ?? 0,
            'true_false' => $distribution['true_false'] ?? 0,
            'short_answer' => $distribution['short_answer'] ?? 0,
            'essay' => $distribution['essay'] ?? 0,
            'average_marks_per_question' => $this->question_count > 0 
                ? round($this->total_marks / $this->question_count, 2) 
                : 0,
        ];
    }

    /**
     * Duplicate this question paper
     */
    public function duplicate()
    {
        $newPaper = $this->replicate();
        $newPaper->code = 'QP-' . strtoupper(\Str::random(6));
        $newPaper->is_published = false;
        $newPaper->published_at = null;
        $newPaper->created_by = auth()->id();
        $newPaper->save();

        // Duplicate questions
        foreach ($this->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->question_paper_id = $newPaper->id;
            $newQuestion->save();
        }

        return $newPaper;
    }

    // ==================== STATIC METHODS ====================
    
    /**
     * Get question papers with statistics
     */
    public static function getWithStatistics($filters = [])
    {
        $query = self::with(['subject', 'class', 'teacher']);
        
        if (isset($filters['class_id'])) {
            $query->byClass($filters['class_id']);
        }
        
        if (isset($filters['subject_id'])) {
            $query->bySubject($filters['subject_id']);
        }
        
        if (isset($filters['term'])) {
            $query->byTerm($filters['term']);
        }
        
        if (isset($filters['academic_year'])) {
            $query->byAcademicYear($filters['academic_year']);
        }
        
        if (isset($filters['status'])) {
            if ($filters['status'] === 'published') {
                $query->published();
            } elseif ($filters['status'] === 'draft') {
                $query->draft();
            }
        }
        
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }
        
        return $query->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($paper) {
                return [
                    'id' => $paper->id,
                    'code' => $paper->code,
                    'title' => $paper->title,
                    'subject' => $paper->subject->name ?? 'N/A',
                    'class' => $paper->class->full_class_name ?? 'N/A',
                    'term' => $paper->term,
                    'academic_year' => $paper->academic_year,
                    'question_count' => $paper->questions_count,
                    'total_marks' => $paper->total_marks,
                    'status' => $paper->status,
                    'is_published' => $paper->is_published,
                    'created_at' => $paper->created_at,
                    'updated_at' => $paper->updated_at,
                ];
            });
    }

    /**
     * Get dropdown options for selection
     */
    public static function getDropdown($classId = null, $subjectId = null)
    {
        $query = self::with(['subject', 'class']);
        
        if ($classId) {
            $query->byClass($classId);
        }
        
        if ($subjectId) {
            $query->bySubject($subjectId);
        }
        
        return $query->orderBy('title')
            ->get()
            ->pluck('full_title', 'id');
    }

    /**
     * Get statistics summary
     */
    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'published' => self::published()->count(),
            'draft' => self::draft()->count(),
            'total_questions' => Question::count(),
            'total_marks' => self::sum('total_marks'),
        ];
    }
}