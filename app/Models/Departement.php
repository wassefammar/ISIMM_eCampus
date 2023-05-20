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


    public function enseignants(){
        return $this->belongsToMany(Enseignant::class,'enseignant_id');
    }

    public function chefDepartement(){
        return $this->belongsToMany(Enseignant::class, 'chef_departements');
    }

}
