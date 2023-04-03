<?php

namespace App\Models;

use App\Models\Classe;
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

    public function classes():HasMany{
        return $this->hasMany(Classe::class, 'id');
    }
}
