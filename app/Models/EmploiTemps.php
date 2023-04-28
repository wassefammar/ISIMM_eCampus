<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploiTemps extends Model
{
    use HasFactory;
     

    protected $fillable=[
      'classe_id',
      'nom'
    ];

    public function classe(){
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function seances(){
        return $this->belongsToMany(SessionMatiere::class, 'emploi_seances');
    }
}
