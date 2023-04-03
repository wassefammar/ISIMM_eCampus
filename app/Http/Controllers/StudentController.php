<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    //
    public function register(Request $request){
        $attrs= $request->validate([
            'name' =>'required|string',
            'prenom'=>'required|string',
            'email'=>'required|email|unique:users,email',
            'date_de_naissance'=>'required|date|before:today|after:1990',
            'password' => 'required|min:6|confirmed',
            'telephone'=>'required|string|min:8',
            'image'=>'required|image|mimes:jpeg,png,jpg,svg'
        ]);
       // $etudiantf=Student::where('email',$attrs['email'])->get();
        if(Student::where('email',$attrs['email'])->exists()){
            return response([
                'message'=>'email déja existant'
            ],409);
        }else{
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
            $etudiant= Student::create([
                'nom'=>$attrs['name'],
                'email'=>$attrs['email'],
                'prenom'=>$attrs['prenom'],
                'date_de_naissance'=>$attrs['date_de_naissance'],
                'password'=>bcrypt($attrs['password']),
                'telephone'=>$attrs['telephone'],
                'image'=>$imageName
            ]);
            if($etudiant){
                Storage::disk('public')->put($imageName, file_get_contents($attrs['image']));
                return response([
                    'message'=>'Compte créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }

        }
       
    }


    
    public function login(Request $request){
        $attrs= $request->validate([
            'email'=>'required|email',
            'password' => 'required|min:6'
        ]);
        if(!Auth::guard('students')->attempt($attrs)){
            return response([
             'message'=>'Vérifier vos informations'
            ], 403);
        }
       //returns user & token in response
        return response([
            'user'=>Auth::guard('students')->user(),
            'token'=>Auth::guard('students')->user()->createToken('secret')->plainTextToken
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



    // changer les informations d'un etudiant
    public function update(Request $request){
        $attrs= $request->validate([
            'name'=>'required|string',
            'prenom'=>'required|string',
            'email'=>'required|string',
            'image'=>'required|image|mimes:jpeg,png,jpg,svg'
        ]);
        if(Student::where('id','!=',auth('sanctum')->user()->id)->where(['email'=>$attrs['email']])->exists()){
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
