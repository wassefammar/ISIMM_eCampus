<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\EnseignantClasse;
use App\Models\EtudiantClasse;
use App\Models\ListParticipants;
use App\Models\Student;
use Illuminate\Http\Request;

class ListParticipantsController extends Controller
{
    //

    public function index(Request $request){
        $attrs=$request->validate([
            'chat_id'=>'required|integer'
        ]);
        $enseignantId=auth('sanctum')->user()->id;
        $chatId=ChatRoom::where('id','=',$attrs['chat_id'])->first('classe_id');

        if($chatId){
            $exists=EnseignantClasse::where('classe_id','=',$chatId->classe_id)->where('enseignant_id','=',$enseignantId)->first();
            if($exists){
                $participantIds=ListParticipants::where('chat_id','=',$attrs['chat_id'])->get('student_id');

                $participants=Student::whereIn('id',$participantIds)->get(['nom','prenom','image']);
                return response([
                    'message'=>'Voilà la liste des participants',
                    'participants'=>$participants
                ],200);
            }
            else{
                return response([
                    'message'=>'Enseignant non associé avec cette classe'
                ],401);
            }

        }else{
            return response([
                'message'=>'chat room non existante'
            ],404);
        }
    }


    public function blockParticipant(Request $request){
        $attrs=$request->validate([
            'chat_id'=>'required|integer',
            'etudiant_id'=>'required|integer'
        ]);
        $etudiantId=$attrs['etudiant_id'];
        $chatId=ChatRoom::where('id','=',$attrs['chat_id'])->first();
        if($chatId){
            $exists=ListParticipants::where('chat_id','=',$chatId->id)->where('student_id','=',$etudiantId)->first();
            if($exists){
                $exists->delete();
                return response([
                    'message'=>'étudiant supprimé de la liste des participants',
                ],200);
            }
            else{
                return response([
                    'message'=>'étudiant non existant dans la liste des participants'
                ],401);
            }

        }else{
            return response([
                'message'=>'chat room non existante'
            ],404);
        }



    }

    public function ajouterParticipant(Request $request){
        $attrs=$request->validate([
            'chat_id'=>'required|integer',
            'etudiant_id'=>'required|integer'
        ]);
        $etudiantId=$attrs['etudiant_id'];
        $chatId=ChatRoom::where('id','=',$attrs['chat_id'])->first();
        if($chatId){
            $exists=ListParticipants::where('chat_id','=',$chatId->id)->where('student_id','=',$etudiantId)->first();
            if($exists){
                return response([
                    'message'=>'étudiant dèja existant dans la liste des participants',
                ],200);
            }
            else{
                $classIds=EtudiantClasse::where('student_id','=',$etudiantId)->get('classe_id');
                $chat=ChatRoom::where('id','=',$chatId->id)->whereIn('classe_id',$classIds)->first();
                if($chat){
                    $ajout=ListParticipants::create([
                        'chat_id'=>$chatId->id,
                        'student_id'=>$etudiantId
                    ]);
                    if($ajout){
                        return response([
                            'message'=>'étudiant ajouté à la liste des participants'
                        ],200);
                    }
                    else{
                        return response([
                            'message'=>'Oops... problème'
                        ],500);
                    }
                }
                return response([
                    'message'=>'étudiant non autorisé à participer dans ce chat'
                ],401);
            }

        }else{
            return response([
                'message'=>'chat room non existante'
            ],404);
        }

    }


    
}
