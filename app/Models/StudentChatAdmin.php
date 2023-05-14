<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentChatAdmin extends Model
{
    use HasFactory;

    protected $table="student_chat_admins";

    protected $fillable=[
        'nom',
        'student_id'
    ];

     public function etudiant(){
        return $this->belongsTo(Student::class, 'student_id');
    } 

    public function messages(){
        return $this->hasMany(ContactEtudiantAdmin::class, 'student_chat_admin_id');
    }

    public function lastmessage(){
        return $this->hasOne(ContactEtudiantAdmin::class, 'student_chat_admin_id')->latest('updated_at');
    }
}
