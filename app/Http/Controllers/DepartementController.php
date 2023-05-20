<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\Enseignant;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index(){
        $departements=Departement::with((['chefDepartement'=>function($query){
                                          $query->select( 'nom', 'prenom');
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
                        
                    }
                    $exist=Departement::where('nom','=',$attrs['nom'])->first();
                    return response([
                        'message'=>'departement créé avec succès'
                    ],200);
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
                        'message'=>'departement créé avec succès'
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
}
