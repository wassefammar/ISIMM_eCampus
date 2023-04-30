<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Annonce;
use App\Models\Classe;
use App\Models\Departement;
use App\Models\Student;
use App\Models\Enseignant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AnnonceController extends Controller
{
    //

    public function index(){
        $annonces=Annonce::withCount('likes','deslikes')->orderBy('created_at', 'desc')->get();
        $annoncesIds=Annonce::all('proprietare_id')->sortDesc();
        if(count($annoncesIds)>0){
            for($i=0;$i<count($annoncesIds);$i++){
                if($annonces[$i]['type_user']===2){
                    $enseignant = Enseignant::find($annoncesIds[$i]->proprietare_id);
                    if($enseignant){
                        $proprietaires[$i]=Enseignant::where('id','=',$annoncesIds[$i]->proprietare_id)->first(['id','departement_id','nom','prenom','image']);
                        $submasques[$i]=Departement::where('id','=',$proprietaires[$i]->departement_id)->first('nom');
                    }
                }
                elseif($annonces[$i]->type_user==="etudiant"){
                    $proprietaires[$i]=Student::where('id','=',$annoncesIds[$i]->proprietare_id)->get(['id','classe_id','nom','prenom','image']);
                    $submasques[$i]=Classe::where('id','=',$proprietaires[$i]->classe_id)->first('nom');

                }
                else{
                    $proprietaires[$i]=Admin::where('id','=',$annoncesIds[$i]->proprietare_id)->get(['id','nom','prenom','image']);
                    $submasques[$i]='admin';
                }
            } 

            return response([
                'message'=>'voila la liste des annonces',
                'annonces'=>$annonces,
                'proprietaires'=>$proprietaires,
                'soutitre'=>$submasques
            ],200);
        }
        else{
            return response([
                'message'=>"Pas d'annonces pour le moment"
            ],404);
        }
    }

    public function storeForStudents(Request $request){
         $attrs=$request->validate([
            'titre'=>'string',
            'description'=>'required|string',
            'image'=>'file'
         ]);
         $proprietaireId=auth('sanctum')->user()->id;
         $proprietaireType=1;
         if($attrs['image']!=null){
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
            $annonce=Annonce::create([
               'proprietaire_id'=>$proprietaireId,
               'type_user'=>$proprietaireType,
               'description'=>$attrs['description'],
               'titre'=>$attrs['titre'],
               'image'=>$imageName
            ]);
            if($annonce){
                Storage::disk('public/annonces')->put($imageName, file_get_contents($attrs['image']));
                return response([
                    'message'=>'Annonce créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ],500);
            }

         }else{
            $annonce=Annonce::create([
               'proprietaire_id'=>$proprietaireId,
               'type_user'=>$proprietaireType,
               'description'=>$attrs['description'],
               'titre'=>$attrs['titre'],
               'image'=>null
            ]);
            if($annonce){
                return response([
                    'message'=>'Annonce créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ],500);
            }
         }

    }



    
    public function storeForEnseignants(Request $request){
        $attrs=$request->validate([
           'titre'=>'string',
           'description'=>'required|string',
           'image'=>'file'
        ]);
        $proprietaireId=auth('sanctum')->user()->id;
        $proprietaireType=2;
        if($request->hasFile('image')){
           $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();

           if($request->has('titre')){
            $annonce=Annonce::create([
                'proprietare_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'titre'=>$attrs['titre'],
                'image'=>$imageName
             ]);
           }
           else{
            $annonce=Annonce::create([
                'proprietare_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'image'=>$imageName
             ]);
           }
         
           if($annonce){
               Storage::disk('public')->put($imageName, file_get_contents($attrs['image']));
               return response([
                   'message'=>'Annonce créé avec succès',
               ],200);
           }else{
               return response([
                   'message'=>'Oops.. problème'
               ],500);
           }

        }else{
           if($request->has('titre')){
            $annonce=Annonce::create([
                'proprietaire_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'titre'=>$attrs['titre'],
                'image'=>null
             ]);
           }
           else{
            $annonce=Annonce::create([
                'proprietare_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'image'=>null
             ]);
           }
           if($annonce){
               return response([
                   'message'=>'Annonce créé avec succès',
               ],200);
           }else{
               return response([
                   'message'=>'Oops.. problème'
               ],500);
           }
        }

   }


   
    public function storeForAdmins(Request $request){
        $attrs=$request->validate([
        'titre'=>'string',
        'description'=>'required|string',
        'image'=>'file'
        ]);
        $proprietaireId=auth('sanctum')->user()->id;
        $proprietaireType=3;
        if($attrs['image']!=null){
            $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
            $annonce=Annonce::create([
                'proprietaire_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'titre'=>$attrs['titre'],
                'image'=>$imageName
            ]);
            if($annonce){
                Storage::disk('public/annonces')->put($imageName, file_get_contents($attrs['image']));
                return response([
                    'message'=>'Annonce créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ],500);
            }

        } else{
            $annonce=Annonce::create([
                'proprietaire_id'=>$proprietaireId,
                'type_user'=>$proprietaireType,
                'description'=>$attrs['description'],
                'titre'=>$attrs['titre'],
                'image'=>null
            ]);
            if($annonce){
                return response([
                    'message'=>'Annonce créé avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. problème'
                ],500);
            }
        }

    }

    public function update(Request $request, $id){
        $attrs=$request->validate([
            'description'=>'string',
            'titre'=>'string',
            'image'=>'file'
        ]);
        $annonce=Annonce::where('id','=',$id)->first();
        if($annonce){
            if($annonce->proprietare_id==auth('sanctum')->user()->id){
                if($request->hasFile('image')){
                    $imageName= Str::random(20).".".$attrs['image']->getClientOriginalExtension();
                    if($request->has('titre')){
                        if($request->has('description')){
                            $annonce->update([
                                'description'=>$attrs['description'],
                                'titre'=>$attrs['titre'],
                                'image'=>$imageName
                            ]);
                            return response([
                                'message'=>'Annonce mise à jour avec succès'
                            ],200);   
                        }
                        else{
                            $annonce->update([
                                'titre'=>$attrs['titre'],
                                'image'=>$imageName
                            ]);
                            return response([
                                'message'=>'Annonce mise à jour avec succès'
                            ],200);   
                        }
                    }
                    else{
                        if($request->has('description')){
                            $annonce->update([
                                'description'=>$attrs['description'],
                                'image'=>$imageName
                            ]);
                            return response([
                                'message'=>'Annonce mise à jour avec succès'
                            ],200);   
                        }
                        else{
                            $annonce->update([
                                'image'=>$imageName
                            ]);
                            return response([
                                'message'=>'Annonce mise à jour avec succès'
                            ],200);   
                        }
                    }

                }
                else{
                        if($request->has('titre')){
                            if($request->has('description')){
                                $annonce->update([
                                    'description'=>$attrs['description'],
                                    'titre'=>$attrs['titre'],
                                ]);
                                return response([
                                    'message'=>'Annonce mise à jour avec succès'
                                ],200);   
                            }
                            else{
                                $annonce->update([
                                    'titre'=>$attrs['titre'],
                                ]);
                                return response([
                                    'message'=>'Annonce mise à jour avec succès'
                                ],200);   
                            }
                        }
                        else{
                            if($request->has('description')){
                                $annonce->update([
                                    'description'=>$attrs['description']
                                    ]);
                                    return response([
                                        'message'=>'Annonce mise à jour avec succès'
                                    ],200);    
                            }
                            else{
                                return response([
                                    'message'=>'verifier vos inputs.'
                                ],422);
                            }
                        }
                    }
            }

        }
    }

    public function destroy($id){
        $annonce=Annonce::find($id);
        if(!$annonce){
            return response([
                'message'=>'Annonce Introuvable',
            ],404);
        }
        elseif($annonce->proprietare_id !=auth('sanctum')->user()->id){
            return response([
                'message'=>'Non autorisé',
            ],403);
        }
        else{
           $annonce->deslikes()->delete();
           $annonce->likes()->delete();
           $annonce->delete(); 

           return response([
            'message'=>'Annonce supprimée avec succès',
            
        ],200);
        }
    }
}