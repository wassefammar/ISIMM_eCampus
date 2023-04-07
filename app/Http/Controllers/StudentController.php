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

    public function fiit(){
        $students=Student::all();
        if(count($students)>0){
            return response()->json([
                'message'=> 'Voila la liste des etudiant',
                'user'=>$students
            ],200);  
        }
        else{
            return response()->json([
                'message'=> 'No data found',
            ],404);
        }
    }
}
