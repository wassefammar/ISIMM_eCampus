<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Enseignant;
use App\Models\FichePresence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classe extends Model
{
    use HasFactory;

    protected $table="classes";
    
    protected $fillable=[
        'nom'
    ];
    public function etudiants(){
        return $this->hasMany(Student::class, 'classe_id');
    }

    public function enseignants()
    {
      return $this->belongsToMany(Enseignant::class, 'enseignant_classes');
    }

    public function departement(){
        return $this->belongsTo(Departement::class, 'departement_id');
    }
    
    public function matieres(){
        return $this->belongsToMany(Matiere::class, 'matiere_classes');
    }

    public function emploiTemps(){
        return $this->hasOne(EmploiTemps::class,'emploi_id');
    }
    
    public function fichePresence(){
        return $this->hasMany(FichePresence::class, 'fichePresence_id');
    }

    public function chatRoom(){
        return $this->hasOne(ChatRoom::class,'chat_id');
    }
}
