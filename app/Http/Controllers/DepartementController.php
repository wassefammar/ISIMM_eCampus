<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;

class DepartementController extends Controller
{
    public function index(){
        $departements=Departement::with((['chefDepartement'=>function($query){
                                              $query->select('id','enseignant_id')
                                                    ->with('enseignant:id,nom,prenom');
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

    }
}
