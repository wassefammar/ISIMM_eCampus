<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Remarque extends Model
{
    use HasFactory;
    protected $table="remarques";

    protected $fillable = [
        'matiere_id',
        'titre',
        'description'
    ];

    public function matiere(){
        return $this->belongsTo(Matiere::class,'matiere_id');
    }
}
