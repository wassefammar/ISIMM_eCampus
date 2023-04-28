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
        'day',
        'startTime',
        'endTime'
    ];

    public function emploi_temps(){
        return $this->belongsToMany(EmploiTemps::class, 'emploi_seances');
    }
}
