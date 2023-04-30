<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    protected $table="likes";
    protected $fillable=[
        'proprietaire_id',
        'annonce_id'  
      ];
  
      public function annonce(){
          return $this->belongsTo(Annonce::class, 'annonce_id');
      }
}
