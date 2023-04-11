<?php

namespace App\Models;

use App\Models\Classe;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Enseignant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table="enseignants";

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'date_de_naissance',
        'telephone',
        'image'
    ];

    public function departement(){
        return $this->belongsTo(Departement::class, 'departement_id');
    }

    public function classes(){
        return $this->belongsToMany(Classe::class, 'enseignant_classes');
    }

    public function matieres()
    {
        return $this->belongsToMany(Matiere::class, 'enseignant_matieres');
    }

/*     public function assignMatiere(Matiere $matiere)
    {
        $this->matieres()->attach($matiere);
    } */


}
