<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    use HasFactory;
    protected $table="cours";

    protected $fillable = [
        'matiere_id',
        'titre',
        'description',
        'file'
    ];

    public function matiere(){
        return $this->belongsTo(Matiere::class,'matiere_id');
    }
}
