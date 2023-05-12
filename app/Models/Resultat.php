<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultat extends Model
{
    use HasFactory;

    protected $table="resultats";

    protected $fillable=[
        'matiere_id',
        'student_id',
        'note_TD',
        'note_TP',
        'note_DS',
        'note_Examen',
        'moyenne',
        'credit'
    ];

    public function matiere(){
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function etudiant(){
        return $this->belongsTo(Student::class, 'student_id');
    }
}
