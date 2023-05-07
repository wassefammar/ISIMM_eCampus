<?php

namespace App\Http\Controllers;

use App\Models\Societe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SocieteController extends Controller
{
    //
    public function index(){
        $societes=Societe::all()->sortDesc();
        if(count($societes)>0){
            return response([
                'message'=>'voilà les sociétés',
                'sociétés'=>$societes
            ],200);
        }
        else{
            return response([
                'message'=>'Rien à afficher',
            ],404);
        }
    }


    public function store(Request $request){
      $attrs=$request->validate([
        'nom' =>'required|string',
        'email'=>'required|email|unique:users,email',
        'telephone'=>'required|string|min:8',
        'image'=>'required|image|mimes:jpeg,png,jpg,svg',
        'adresse'=>'required|string',
        'a_propos'=>'required|string',
        'site_web'=>'required|string'
      ]);

        if(Societe::where('email',$attrs['email'])->exists()){
            return response([
                'message'=>'email déja existant'
            ],409);
        }else{
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
            $societe= Societe::create([
                'nom'=>$attrs['nom'],
                'email'=>$attrs['email'],
                'adresse'=>$attrs['adresse'],
                'a_propos'=>$attrs['a_propos'],
                'site_web'=>$attrs['site_web'],
                'telephone'=>$attrs['telephone'],
                'image'=>$imageName
            ]);
            if($societe){
                Storage::disk('public')->put($imageName, file_get_contents($attrs['image']));
                return response([
                    'message'=>'Société créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }

        }

    }

    public function update(Request $request, $id){
        $attrs=$request->validate([
            'email'=>'required|email|unique:users,email',
            'telephone'=>'required|string|min:8',
            'adresse'=>'required|string',
            'site_web'=>'required|string'
          ]);

          $societe=Societe::where('id','=',$id)->first();
          if($societe){
            if(Societe::where('id','!=',$id)->where('email',$attrs['email'])->exists()){
                return response([
                    'message'=>'email déja existant'
                ],409);
            }else{
                $societe->update([
                    'email'=>$attrs['email'],
                    'adresse'=>$attrs['adresse'],
                    'site_web'=>$attrs['site_web'],
                    'telephone'=>$attrs['telephone'],
                ]);
                if($societe){
                    return response([
                        'message'=>'Société mise à jour avec succès',
                    ],200);
                }else{
                    return response([
                        'message'=>'Oops.. il ya un problème',
                    ],500);
                }
    
            }
          }
    }


    public function destroy($id){
        $societe=Societe::where('id','=',$id)->first();
        if($societe){
          unlink(storage_path().'/app/public/'.$societe->image);
          $societe->delete();

          return response([
            'message'=>'societe supprimée avec succès'
          ],200);

        }else{
          return response([
            'message'=>'societe inexistante'
          ],404);
  
         }
        

    }
}
