<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table="students";    
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'date_de_naissance',
        'telephone',
        'image'
    ];
    public function classe(): HasOne{
       return $this->hasOne(Classe::class,'id');
    }
    
}
