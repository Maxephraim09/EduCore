<?php

namespace App\Imports;

use App\Models\Result;
use App\Models\Student;
use App\Models\GradeScale;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScoresImport implements WithHeadingRow
{
    public function headingRow(): int
    {
        return 1;
    }
}