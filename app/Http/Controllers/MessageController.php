<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //

    public function index($id){
        $messages=Message::where('chat_id','=',$id)->orderBy('created_at', 'desc')->get();
        if(count($messages)>0){
            return response([
                'messages'=>$messages
            ],200);
        }
        else{
            return response([
                'messages'=>'Rien à afficher'
            ],404);
        }  
    }
    

    public function store(Request $request){
        $attrs=$request->validate([
            'text'=>'required|string',
            'chat_id'=>'required|integer'
        ]);
        if(ChatRoom::where('id','=',$attrs['chat_id'])->exists()){
            $message=Message::create([
                'user_id'=>auth('sanctum')->user()->id,
                'chat_id'=>$attrs['chat_id'],
                'text'=>$attrs['text']
            ]);
            if ($message) {
                $message->chatRoom()->associate($attrs['chat_id']);
                return response([
                    'message'=>'message créé avec succès.'
                ],200);        
            } else {
                return response([
                    'message'=>'Ooups... problème'
                ],500);
            }
        }else{
            return response([
                'message'=>'Chat room inexistante'
            ],404);
        }


    }
}
