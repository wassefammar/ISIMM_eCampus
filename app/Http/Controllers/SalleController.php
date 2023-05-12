<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    //
    public function index(){
       $salles=Salle::all(['nom']);
       if(count($salles)>0){
        return response([
            'message'=>'voilà les salles',
            'salles'=>$salles
        ],200);
       }else{
        return response([
            'message'=>'pas de salles',
        ],404);
       }
    }

    public function store(Request $request){
        $attrs=$request->validate([
            'nom'=>'required|string'
        ]);

        if(Salle::where('nom','=',$attrs['nom'])->exists()){
            return response([
                'message'=>'salle existe déja.'
            ],409);
        }
        else{
            $salle=Salle::create([
                'nom'=>$attrs['nom']
            ]);
            if($salle){
                return response([
                    'message'=>'salle crée avec succès'
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
