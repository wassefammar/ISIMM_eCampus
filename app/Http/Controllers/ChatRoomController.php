<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use Illuminate\Http\Request;
use App\Models\EtudiantClasse;
use App\Models\EnseignantClasse;

class ChatRoomController extends Controller
{
    //
    public function index(){
        $enseignantId=auth('sanctum')->user()->id;
        $classeIds=EnseignantClasse::where('enseignant_id','=',$enseignantId)->get('classe_id');
        if(count($classeIds)>0){
            for($i=0;$i<count($classeIds);$i++){
                $chatRooms[$i]=ChatRoom::where('classe_id','=',$classeIds[$i]->classe_id)
                                    ->with('lastmessages')
                                    ->latest('updated_at')
                                    ->first();
            }

            if(count($chatRooms)>0){
                return  response([
                    'message'=>'Voila les chat rooms',
                    'chat_Room'=>$chatRooms
                ],200);
            }

            else{
                return  response([
                    'message'=>'Chat room vide',
                    'chat_Room'=>$chatRooms
                ],200); 
            }



        }else{
            return  response([
                'message'=>'Enseignant non assigné à aucune classe'
            ],404);
        }



    }

    public function indexForStudents(){
        $enseignantId=auth('sanctum')->user()->id;
        $classeIds=EtudiantClasse::where('student_id','=',$enseignantId)->get('classe_id');
        if(count($classeIds)>0){
            for($i=0;$i<count($classeIds);$i++){
                $chatRooms[$i]=ChatRoom::where('classe_id','=',$classeIds[$i]->classe_id)
                                    ->with('lastmessages')
                                    ->latest('updated_at')
                                    ->first();
            }

            if(count($chatRooms)>0){
                return  response([
                    'message'=>'Voila les chat rooms',
                    'chat_Room'=>$chatRooms
                ],200);
            }

            else{
                return  response([
                    'message'=>'Chat room vide',
                    'chat_Room'=>$chatRooms
                ],200); 
            }



        }else{
            return  response([
                'message'=>'Enseignant non assigné à aucune classe'
            ],404);
        }



    }


    
}
