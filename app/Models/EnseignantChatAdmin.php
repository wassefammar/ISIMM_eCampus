<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnseignantChatAdmin extends Model
{
    use HasFactory;
    protected $table="enseignant_chat_admins";

    protected $fillable=[
        'nom',
        'enseignant_id'
    ];

     public function enseignant(){
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    } 

    public function messages(){
        return $this->hasMany(ContactEnseignantAdmin::class, 'enseignant_chat_admin_id');
    }

    public function lastmessage(){
        return $this->hasOne(ContactEnseignantAdmin::class, 'enseignant_chat_admin_id')->latest('updated_at');
    }
}
