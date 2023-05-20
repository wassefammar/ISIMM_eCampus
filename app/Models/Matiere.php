<?php

namespace App\Models;

use App\Models\FichePresence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Matiere extends Model
{
    use HasFactory;
    
    protected $table="matieres";
    protected $fillable = [
        'nom'
    ];
    public function enseignants()
    {
        return $this->belongsToMany(Enseignant::class, 'enseignant_matieres');
    }


    public function classes(){
        return $this->belongsToMany(Classe::class, 'matiere_classes');
    }

    public function remarques(){
        return $this->hasMany(Remarque::class, 'matiere_id');
    }

    public function cours(){
        return $this->hasMany(Cours::class, 'matiere_id');
    }

    public function exercices(){
        return $this->hasMany(Exercices::class, 'matiere_id');
    }

    public function examens(){
        return $this->hasMany(Examen::class, 'matiere_id');
    }

    public function classDocuments(){
        return $this->hasMany(ClassDocument::class, 'matiere_id');
    }

    public function fichePresence(){
        return $this->hasMany(FichePresence::class, 'fichePresence_id');
    }

}
