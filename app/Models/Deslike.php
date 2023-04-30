<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deslike extends Model
{
    use HasFactory;

    protected $table="deslikes";
    protected $fillable=[
        'proprietaire_id',
        'proprietaire_type',
        'annonce_id'  
      ];
  
      public function annonce(){
          return $this->belongsTo(Annonce::class, 'annonce_id');
      }
}
