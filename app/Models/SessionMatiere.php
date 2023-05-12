<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionMatiere extends Model
{
    use HasFactory;

    protected $table='session_matieres';
    
    protected $fillable=[
        'matiere_id',
        'enseignant_id',
        'salle_id',
        'day',
        'startTime',
        'endTime'
    ];

    public function emploi_temps(){
        return $this->belongsToMany(EmploiTemps::class, 'emploi_seances');
    }
    public function matiere(){
        return $this->belongsTo(Matiere::class, 'matiere_id');
    }

    public function enseignant(){
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }
    public function salle(){
        return $this->belongsTo(Salle::class, 'salle_id');
    }
}
