<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassDocument extends Model
{
    use HasFactory;
    protected $table="class_documents";

    protected $fillable = [
        'matiere_id',
        'student_id',
        'flag',
        'titre',
        'description',
        'file'
    ];

    public function matiere(){
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function etudiant(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
