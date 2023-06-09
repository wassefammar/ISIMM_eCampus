<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use App\Models\EnseignantChatAdmin;
use App\Models\ContactEnseignantAdmin;

class EnseignantChatAdminController extends Controller
{
    //
    public function indexForAdmin(){
        $chats=EnseignantChatAdmin::with('enseignant:id,nom,prenom,image')->with('lastmessage')->orderBy('updated_at', 'desc')->get();

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
       $exists=EnseignantChatAdmin::where('id','=',$chatId)->where('enseignant_id','=',$etudiantId)->first();
       if($exists){
           $message=ContactEnseignantAdmin::create([
               'enseignant_chat_admin_id'=>$chatId,
               'admin_id'=>$adminId->id,
               'enseignant_id'=>$etudiantId,
               'sender_id'=>auth('sanctum')->user()->id,
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
           $etudiant=Enseignant::where('id','=',$etudiantId)->first(['nom','prenom']);
           $chat=EnseignantChatAdmin::create([
               'nom'=>$etudiant->nom.' '.$etudiant->prenom,
               'enseignant_id'=>$etudiantId
           ]);
           if($chat){
               $chatId=EnseignantChatAdmin::where('enseignant_id','=',$etudiantId)->first();
               $message=ContactEnseignantAdmin::create([
                   'enseignant_chat_admin_id'=>$chatId,
                   'admin_id'=>$adminId->id,
                   'enseignant_id'=>$etudiantId,
                   'sender_id'=>auth('sanctum')->user()->id,
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

       $chat=EnseignantChatAdmin::find($attrs['chat_id'])->first();

       if($chat){
           $message=ContactEnseignantAdmin::create([
               'enseignant_chat_admin_id'=>$attrs['chat_id'],
               'admin_id'=>auth('sanctum')->user()->id,
               'enseignant_id'=>$chat->enseignant_id,
               'sender_id'=>auth('sanctum')->user()->id,
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
    $chat=EnseignantChatAdmin::where('id','=',$id)->first();
    if($chat){
       $messages=ContactEnseignantAdmin::where('enseignant_chat_admin_id',$chat->id)
                                    ->with('enseignant:id,nom,prenom,image')
                                    ->with('admin:id,nom,prenom,image')
                                    ->orderBy('updated_at', 'desc')
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
