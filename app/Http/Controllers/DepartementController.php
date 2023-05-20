<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Enseignant;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index(){
        $departements=Departement::with((['chefDepartement'=>function($query){
                                          $query->select( 'nom', 'prenom')->first();
                                        }]))
                                 ->withCount('enseignants')
                                 ->get();
        if(count($departements)>0){
            return response([
                'message'=>'Voilà la liste des departements',
                'departements'=>$departements
            ],200);
        }
        else{
            return response([
                'message'=>'pas de departements',
            ],404);
        }                         
        
    }

    public function store(Request $request){
        $attrs=$request->validate([
            'nom'=>'required|string',
            'chefDepartement'=>'integer'
        ]);
        if(Departement::where('nom','=',$attrs['nom'])->exists()){
           return response([
            'message'=>'departement existe dèjà'
           ],409);

        }
        else{
            if($request->has('chefDepartement')){
                $departement=Departement::create([
                    'nom'=>$attrs['nom']
                ]);
                if($departement){
                    $chefDep=Enseignant::where('id','=',$attrs['chefDepartement'])->first('id');
                    if($chefDep){
                        $exist=Departement::where('nom','=',$attrs['nom'])->first();
                        $exist->chefDepartement()->attach($chefDep->id);
                        return response([
                            'message'=>'departement créé avec succès'
                        ],200);
                    }
                    else{
                        return response([
                            'message'=>'departement créé avec succès sans chef de departement'
                        ],200);
                    }

                }
                else{
                    return response([
                        'message'=>'Oops problème de serveur'
                    ],500); 
                }
            }else{
                $departement=Departement::create([
                    'nom'=>$attrs['nom']
                ]);
                if($departement){
                    return response([
                        'message'=>'departement créé avec succès sans chef de departement'
                    ],200);
                }
                else{
                    return response([
                        'message'=>'Oops problème de serveur'
                    ],500); 
                }  
            }
        }
    }


    public function update(Request $request, $id){
        $attrs=$request->validate([
            'nom'=>'required|string',
            'chefDepartement'=>'integer'
        ]);
        $departement=Departement::where('id','=',$id)->first();
        if($departement){
            if($request->has('chefDepartement')){
                $departement->update([
                    'nom'=>$attrs['nom']
                ]);
                 $chefDep=Enseignant::where('id','=',$attrs['chefDepartement'])->first('id');
                 if($chefDep){
                    $departement->chefDepartement()->attach($chefDep->id);
                    return response([
                        'message'=>'departement mise à jour avec succès'
                    ],200);
                    }
                else{
                    return response([
                       'message'=>'departement mise à jour avec succès sans chef de departement'
                    ],200);
                }

            }else{
                $departement->update([
                    'nom'=>$attrs['nom']
                ]);
                    return response([
                        'message'=>'departement créé avec succès sans chef de departement'
                    ],200);
                

            }

        }
        else{
            return response([
                'message'=>'departement non existant'
            ],404);
        
        }
    }

    public function destroy($id){
        $departement=Departement::where('id','=',$id)->first();
        if($departement){
            $departement->delete();
            return response([
                'message'=>'deparetment supprimé avec succès.'
            ],200);
        }else{
            return response([
                'message'=>'deparetment non existant.'
            ],200);
        }
    }
}
