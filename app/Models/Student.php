<?php

namespace App\Models;

use App\Models\Classe;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table="students";    
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'date_de_naissance',
        'telephone',
        'image'
    ];
    public function classe(){
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'matieres_etudiants');
    }

    public function classDocuments(){
        return $this->hasMany(ClassDocument::class, 'student_id');
    }
    
}
