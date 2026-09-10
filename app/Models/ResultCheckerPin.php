<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultCheckerPin extends Model
{
    protected $fillable = ['student_id', 'exam_id', 'pin', 'generated_by'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }
}
