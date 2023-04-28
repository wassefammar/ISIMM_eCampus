<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichePresence extends Model
{
    use HasFactory;

    protected $table='fiche_presences';
    protected $fillable=[
        'matiere_id',
        'classe_id',
        'enseignant_id',
    ];

    public function matiere(){
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function classe(){
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function enseignant(){
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    public function listEtudiant(){
        return $this->hasMany(ListPresence::class, 'listEtudiant_id');
    } 
}
