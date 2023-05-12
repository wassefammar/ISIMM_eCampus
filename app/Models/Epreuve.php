<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{
    use HasFactory;

    protected $table="epreuves";
    protected $fillable=[
        'classe_id',
        'matiere_id',
        'enseignant_id',
        'salle_id',
        'date',
        'startTime',
        'endTime'
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

    public function salle(){
        return $this->belongsTo(Salle::class, 'salle_id');
    }


}
