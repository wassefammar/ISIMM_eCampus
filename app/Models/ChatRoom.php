<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $table="chat_rooms";

    protected $fillable=[
        'classe_id',
        'name'
    ];

    public function classe(){
        return $this->belongsTo(Classe::class, 'classe_id');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'chat_id');
    }

    public function lastmessages(){
        return $this->hasOne(Message::class, 'chat_id')->latest('updated_at');
    }
}
