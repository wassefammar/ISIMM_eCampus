<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEnseignantAdmin extends Model
{
    use HasFactory;

    protected $table="contact_enseignant_admins";

    protected $fillable=[
        'enseignant_chat_admin_id',
        'enseignant_id',
        'admin_id',
        'text'
    ];

    public function chat(){
        return $this->belongsTo(EnseignantChatAdmin::class, 'enseignant_chat_admin_id');
    }
    public function enseignant(){
        return $this->belongsTo(Enseignant::class, 'enseignant_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
