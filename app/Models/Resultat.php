<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultat extends Model
{
    use HasFactory;

    protected $table="resultats";

    protected $fillable=[
        'epreuve_id',
        'etudiant_id',
        'note'
    ];

    public function epreuve(){
        return $this->belongsTo(Epreuve::class, 'epreuve_id');
    }

    public function etudiant(){
        return $this->belongsTo(Student::class, 'etudiant_id');
    }
}
