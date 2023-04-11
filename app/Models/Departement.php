<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;
    protected $table='departements';
    protected $fillable=[
        'nom'
    ];

    public function classes(){
        return $this->belonsToMany(Classe::class,'classe_id');
    }

    public function enseignants(){
        return $this->belongsToMany(Enseignant::class,'enseignant_id');
    }

}
