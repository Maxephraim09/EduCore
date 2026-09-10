<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationDocument extends Model
{
    use HasFactory;

    protected $table = 'application_documents';

    protected $fillable = [
        'application_id',
        'document_type',
        'document_name',
        'file_path',
        'mime_type',
        'file_size',
        'is_verified',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
