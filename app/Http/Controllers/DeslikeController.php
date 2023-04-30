<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use App\Models\Deslike;
use Illuminate\Http\Request;

class DeslikeController extends Controller
{
    //
    public function deslikeOrUndeslike($id){
        $annonce=Annonce::find($id);
        if(!$annonce){
            return response([
                'message'=>'Annonce inexistante',
            ],403);
        }
        $deslike= $annonce->deslikes()->where('proprietaire_id', auth('sanctum')->user()->id)->first();

        if(!$deslike){
            Deslike::create([
                'annonce_id'=>$id,
                'proprietaire_id'=>auth('sanctum')->user()->id
            ]);
            return response([
                'message'=>'desliked'
            ],200);
        }
        else{
            $deslike->delete();
            return response([
                'message'=>'undesliked'
            ],200);
        }
    }
}
