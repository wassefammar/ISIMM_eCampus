<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RapportPFE extends Model
{
    use HasFactory;

    protected $table="rapport_p_f_e_s";

    protected $fillable=[
        'titre',
        'description',
        'annee',
        'fichier'
    ];
}
