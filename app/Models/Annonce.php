<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Annonce extends Model
{
    use HasFactory;

    protected $fillable=[
        'proprietare_id',
        'type_user',
        'description',
        'titre',
        'image',
    ];

    public function likes(){
        return $this->hasMany(Like::class, 'annonce_id');
    } 

    public function deslikes(){
        return $this->hasMany(Deslike::class, 'annonce_id');
    }


    
}
