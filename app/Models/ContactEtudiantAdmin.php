<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactEtudiantAdmin extends Model
{
    use HasFactory;

    protected $table="contact_etudiant_admins";

    protected $fillable=[
        'student_chat_admin_id',
        'student_id',
        'admin_id',
        'sender_id',
        'text'
    ];

    public function chat(){
        return $this->belongsTo(StudentChatAdmin::class, 'student_chat_admin_id');
    }
    public function etudiant(){
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
