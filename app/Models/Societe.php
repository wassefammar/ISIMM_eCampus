<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Societe extends Model
{
    use HasFactory;

    protected $table="societes";

    protected $fillable=[
        'nom',
        'image',
        'a_propos',
        'adresse',
        'email',
        'site_web',
        'telephone'
    ];

    public function pfeBook(){
        return $this->hasMany(PFEBook::class, 'societe_id');
    }
}
