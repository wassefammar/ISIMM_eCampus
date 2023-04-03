<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    //
    
    public function register(Request $request){
        $attrs= $request->validate([
            'name' =>'required|string',
            'email'=>'required|email|unique:users,email',
            'prenom'=>'required|string',
            'date_de_naissance'=>'required|date|before:today|after:1990',
            'password' => 'required|min:6|confirmed',
            'telephone'=>'required|string|min:8',
            'image'=>'required|image|mimes:jpeg,png,jpg,svg'
        ]);
        if(Admin::where('email',$attrs['email'])->exists()){
            return response([
                'message'=>'email déja existant'
            ],409);
         }else{
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
            $enseignant= Admin::create([
                'nom'=>$attrs['name'],
                'prenom'=>$attrs['prenom'],
                'email'=>$attrs['email'],
                'date_de_naissance'=>$attrs['date_de_naissance'],
                'password'=>bcrypt($attrs['password']),
                'telephone'=>$attrs['telephone'],
                'image'=>$imageName
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


    }


    
    public function login(Request $request){
        $attrs= $request->validate([
            'email'=>'required|email',
            'password' => 'required|min:6'
        ]);
        if(!Auth::guard('admins')->attempt($attrs)){
            return response([
             'message'=>'Vérifier vos informations',
            ], 403);
        }
       //returns user & token in response
        return response([
            'user'=>Auth::guard('admins')->user(),
            'token'=>Auth::guard('admins')->user()->createToken('secret')->plainTextToken
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
        //$EnseignantIds=Enseignant::where('id','!=',auth('sanctum')->user()->id)->get(['id']);
        if(Admin::where('id','!=',auth('sanctum')->user()->id)->where(['email'=>$attrs['email']])->exists()){
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
