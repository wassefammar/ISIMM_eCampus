<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\ContactEtudiantAdmin;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentChatAdmin;

class StudentChatAdminController extends Controller
{
    //

    public function indexForAdmin(){
         $chats=StudentChatAdmin::with('etudiant:id,nom,prenom,image')->with('lastmessage')->orderBy('updated_at')->get();

         if(count($chats)>0){
           return response([
            'message'=>'voilà les chats',
            'chats'=>$chats
           ],200);
         }

         else{
            return response([
                'message'=>'chats vide'
               ],404);
         }
    }


    public function contacterAdmin(Request $request){
        $attrs=$request->validate([
            'chat_id'=>'required|integer',
            'text'=>'required|string'
        ]);

        $etudiantId=auth('sanctum')->user()->id;
        $chatId=$attrs['chat_id'];
        $adminId=Admin::first('id');
        $exists=StudentChatAdmin::where('id','=',$chatId)->where('student_id','=',$etudiantId)->first();
        if($exists){
            $message=ContactEtudiantAdmin::create([
                'student_chat_admin_id'=>$chatId,
                'admin_id'=>$adminId->id,
                'student_id'=>$etudiantId,
                'text'=>$attrs['text']
            ]);
            if($message){
                return response([
                    'message'=>'message envoyé avec succès'
                ]);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ]);
            }

        }else{
            $etudiant=Student::where('id','=',$etudiantId)->first(['nom','prenom']);
            $chat=StudentChatAdmin::create([
                'nom'=>$etudiant->nom.' '.$etudiant->prenom,
                'student_id'=>$etudiantId
            ]);
            if($chat){
                $chatId=StudentChatAdmin::where('student_id','=',$etudiantId)->first();
                $message=ContactEtudiantAdmin::create([
                    'student_chat_admin_id'=>$chatId,
                    'admin_id'=>$adminId->id,
                    'student_id'=>$etudiantId,
                    'text'=>$attrs['text']
                ]);
                if($message){
                    return response([
                        'message'=>'message envoyé avec succès'
                    ]);
                }else{
                    return response([
                        'message'=>'Oops.. problème'
                    ]);
                }
    
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ]);
    
            }

        }

    }

    public function repondreEtudiant(Request $request){
        $attrs=$request->validate([
            'chat_id'=>'required|integer',
            'text'=>'required|string'
        ]);

        $chat=StudentChatAdmin::find($attrs['chat_id'])->first();

        if($chat){
            $message=ContactEtudiantAdmin::create([
                'student_chat_admin_id'=>$attrs['chat_id'],
                'admin_id'=>auth('sanctum')->user()->id,
                'student_id'=>$chat->student_id,
                'text'=>$attrs['text']
            ]);
            if($message){
                return response([
                    'message'=>'message envoyé avec succès'
                ]);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ]);
            }
        }
    }



    public function chatMessages($id){
      $chat=StudentChatAdmin::where('id','=',$id)->first();
      if($chat){
         $messages=ContactEtudiantAdmin::where('student_chat_admin_id',$chat->id)
                                      ->with('etudiant:id,nom,prenom,image')
                                      ->with('admin:id,nom,prenom,image')
                                      ->get();

            if(count($messages)>0){
                return response([
                    'message'=>'voilà la liste des messages',
                    'messages'=>$messages
                ],200);
            } 
            else{
                return response([
                    'message'=>'chat vide',
                ],200);
            }                            
        }
        else{
            return response([
                'message'=>'chat inexistant',
            ],404);
        } 
    }



}
