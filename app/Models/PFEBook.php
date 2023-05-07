<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PFEBook extends Model
{
    use HasFactory;

    protected $table="p_f_e_books";

    protected $fillable=[
        'titre',
        'description',
        'societe_id',
        'fichier'
    ];

    public function societe(){
        return $this->belongsTo(Societe::class, 'societe_id');
    }
}
