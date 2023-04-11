<?php

namespace App\Http\Controllers;

use App\Models\EnseignantMatiere;
use App\Models\Matiere;
use Illuminate\Http\Request;

class EnseignantMatiereController extends Controller
{
    //

    public function index(){
        $enseignantId=auth('sanctum')->user()->id;
        $matieresId=EnseignantMatiere::where('enseignant_id','=',$enseignantId)->get();
        if(count($matieresId)>0){
            $matieres=Matiere::where('id','=',$matieresId)->get();
            return response([
                'message'=>'Voilà les matieres',
                'matieres'=>$matieres
            ],200);
        }
        elseif(count($matieresId)==0){
            return response([
                'message'=>'Les matieres sont en cours de traitement...'
            ],200);
        }
        else{
            return response([
                'message'=>'Oops...il y a un problème'
            ],500);   
        }
        
    }
}
