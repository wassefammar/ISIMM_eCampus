<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Annonce;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    //
    public function likeOrUnlike($id){
        $annonce=Annonce::find($id);
        if(!$annonce){
            return response([
                'message'=>'Annonce inexistante',
            ],403);
        }
        $like= $annonce->likes()->where('proprietaire_id', auth('sanctum')->user()->id)->first();

        if(!$like){
            Like::create([
                'annonce_id'=>$id,
                'proprietaire_id'=>auth('sanctum')->user()->id
            ]);
            return response([
                'message'=>'liked'
            ],200);
        }
        else{
            $like->delete();
            return response([
                'message'=>'unliked'
            ],200);
        }
    }
}
