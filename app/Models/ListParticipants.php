<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListParticipants extends Model
{
    use HasFactory;

    protected $table="list_participants";

    protected $fillable=[
        'chat_id',
        'student_id'
    ];

    public function chat(){
        return $this->belongsTo(ChatRoom::class, 'chat_id');
    }




}
