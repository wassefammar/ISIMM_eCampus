<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function departement(){
        return $this->belongsTo(Departement::class, 'departement_id');
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

}
