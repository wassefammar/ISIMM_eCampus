<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //
    public function store(Request $request){
        $attrs=$request->validate([
            'text'=>'required|string',
            'chat_id'=>'required|integer'
        ]);
/*         $user = auth('sanctum')->user();
        $message = new Message;
        $message->text = $request->input('text');
        $message->user_id = $user->id;
        $message->save(); */
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


        



        //return response()->json(['success' => true]);
    }
}
