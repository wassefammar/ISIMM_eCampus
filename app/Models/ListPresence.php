<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListPresence extends Model
{
    use HasFactory;

    protected $table='list_presences';
    
    protected $fillable=[
        'fiche_presence',
        'sessionMatiere_id',
        'date',
        'student_id',
        'status',
    ];
 


    public function fichePresence(){
        return $this->belongsTo(FichePresence::class, 'fichePresence_id');
    }
}
