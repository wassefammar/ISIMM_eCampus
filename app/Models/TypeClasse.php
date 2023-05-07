<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeClasse extends Model
{
    use HasFactory;

    protected $table="type_classes";

    protected $fillable=[
        'type'
    ];

    public function classe(){
        return $this->hasMany(Classe::class, 'type_id');
    }
}
