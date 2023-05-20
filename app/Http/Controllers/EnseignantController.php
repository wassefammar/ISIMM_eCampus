<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EnseignantController extends Controller
{
    //
    public function listEnseignants(){
        $enseignants=Enseignant::with('classes:id,nom')->with('matieres:id,nom')->get(['id','nom','prenom','image','email','telephone']);

        if(count($enseignants)>0){
            return response([
                'message'=>'Voila la liste des enseignants',
                'enseignants'=>$enseignants
            ],200);
        }
        else{
            return response([
                'message'=>"pas d'enseignants pour le moment"
            ],404);
        }
    }


    public function modifierEnseignant(Request $request, $id){
        $enseignant=Enseignant::where('id','=',$id)->first();
        if($enseignant){
            $attrs= $request->validate([
                'name'=>'required|string',
                'prenom'=>'required|string',
                'email'=>'required|email',
                'telephone'=>'required|string|min:8',
            ]);
            if(Enseignant::where('id','!=',$id)->where(['email'=>$attrs['email']])->exists()){
                return response([
                    'message'=>'email déja existant'
                ],409);
            }else{      
                $enseignant->update([
                    'nom'=>$attrs['name'],
                    'prenom'=>$attrs['prenom'],
                    'email'=>$attrs['email'],
                ]);    
                return response()->json([
                    'message'=> 'Mise à jour avec succès',
                ],200);
            }
        
        }
        else{
            return response([
                'message'=>'enseignant non existant'
            ],404);
        }
    }


    public function supprimerEnseignant($id){
        $enseignant=Enseignant::where('id','=',$id)->first();
        if($enseignant){
           $enseignant->delete();
           return response([
            'message'=>'enseignant supprimé avec succès'
           ],200);
        }else{
            return response([
                'message'=>'enseignant non existant'
            ],404);
        }
    }


    public function register(Request $request){
        $attrs= $request->validate([
            'name' =>'required|string',
            'email'=>'required|email|unique:users,email',
            'prenom'=>'required|string',
            'date_de_naissance'=>'required|date|before:today|after:1990',
            'password' => 'required|min:6|confirmed',
            'telephone'=>'required|string|min:8',
            'image'=>'required|image|mimes:jpeg,png,jpg,svg',
            'departement'=>'required|string'
        ]);
        if(Enseignant::where('email',$attrs['email'])->exists()){
            return response([
                'message'=>'email déja existant'
            ],409);
         }else{
            $departement=Departement::where('nom','=',$attrs['departement'])->first();
            if($departement){
                $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
                $enseignant= Enseignant::create([
                    'nom'=>$attrs['name'],
                    'prenom'=>$attrs['prenom'],
                    'email'=>$attrs['email'],
                    'date_de_naissance'=>$attrs['date_de_naissance'],
                    'password'=>bcrypt($attrs['password']),
                    'telephone'=>$attrs['telephone'],
                    'image'=>$imageName,
                    'departement_id'=>$departement->id
                ]);
                if($enseignant){
                    Storage::disk('public')->put($imageName, file_get_contents($attrs['image']));
                    return response([
                        'message'=>'Compte créé avec succès'
                    ],200);
                }
                else{
                    return response([
                        'message'=>'Oops... il ya une problème'
                    ],500); 
                }
            }
            else{
                return response([
                    'message'=>'Departement inexistatnt'
                ],500); 
            }

    
         }


    }


    
    public function login(Request $request){
        $attrs= $request->validate([
            'email'=>'required|email',
            'password' => 'required|min:6'
        ]);
        if(!Auth::guard('enseignant')->attempt($attrs)){
            return response([
             'message'=>'Vérifier vos informations',
            ], 403);
        }
       //returns user & token in response
        return response([
            'user'=>Auth::guard('enseignant')->user(),
            'token'=>Auth::guard('enseignant')->user()->createToken('secret')->plainTextToken
        ],200);
    }



    public function logout(Request $request){
        auth('sanctum')->user()->tokens()->delete();
        return response()->json([
            'message'=> 'Déconnecté'
        ],200);
    }



    //Cliquer sur l'icon de profile 
    public function user(){
        $user=auth('sanctum')->user();
        return response([
            'user'=>$user
        ], 200);
    }



    // changer les informations d'un enseignant
    public function update(Request $request){
        $attrs= $request->validate([
            'name'=>'required|string',
            'prenom'=>'required|string',
            'email'=>'required|email',
            'image'=>'required|image|mimes:jpeg,png,jpg,svg|max:1999'
        ]);
        if(Enseignant::where('id','!=',auth('sanctum')->user()->id)->where(['email'=>$attrs['email']])->exists()){
            return response([
                'message'=>'email déja existant'
            ],409);
        }else{
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension(); 
  
            auth('sanctum')->user()->update([
                'nom'=>$attrs['name'],
                'prenom'=>$attrs['prenom'],
                'email'=>$attrs['email'],
                'image'=>$imageName
            ]);
            Storage::disk('public')->put($imageName, file_get_contents($attrs['image']));

            return response()->json([
                'message'=> 'Mise à jour avec succès',
                'user'=>auth('sanctum')->user()
            ],200);
        }
    }

}
