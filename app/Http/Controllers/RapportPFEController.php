<?php

namespace App\Http\Controllers;

use App\Models\RapportPFE;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RapportPFEController extends Controller
{
    //


    public function index(){
        $rapports=RapportPFE::all();
        if(count($rapports)>0){
            return response([
                'message'=>'voilà les rapports',
                'rapports'=>$rapports
            ],200);
        }
        else{
            return response([
                'message'=>'pas de rapports pour le moment',
            ],404); 
        }
    }
    public function store(Request $request){
        $attrs=$request->validate([
            'titre'=>'string|required',
            'description'=>'string',
            'annee'=>'required|date_format:Y',
            'rapport'=>'required|file'
        ]);
        $fileName= Str::random(20).".".$attrs['rapport']->getClientOriginalExtension();
        if($request->has('description')){
            $fichier=RapportPFE::create([
                'titre'=>$attrs['titre'],
                'description'=>$attrs['description'],
                'annee'=>$attrs['annee'],
                'fichier'=>$fileName
            ]);
        }
        else{
            $fichier=RapportPFE::create([
                'titre'=>$attrs['titre'],
                'description'=>null,
                'annee'=>$attrs['annee'],
                'fichier'=>$fileName
            ]);
        }

        if($fichier){
            Storage::disk('public')->put($fileName, file_get_contents($attrs['rapport']));
            return response([
                'message'=>'rapport ajouté avec succès',
            ],200);
        }else{
            return response([
                'message'=>'Oops.. il ya un problème',
            ],500);
        }
    }

    public function download(Request $request){
                //PDF file is stored under project/public/download/info.pdf
        $attrs=$request->validate([
            'filename'=>'required|string'
        ]);
        $fullName=$attrs['filename'].'.pdf';       
        if(RapportPFE::where('fichier','=',$fullName)->exists()){
            $fichier= storage_path().'/app/public/'. $fullName;
            $headers = array(
                'Content-Type: application/pdf',
            );

            return response()->download($fichier, $fullName, $headers);

        }
        else{
            return response([
                'message'=>'Document non existant',
            ],404);

        }

    }


    public function destroy($id){
      $rapport=RapportPFE::where('id','=',$id)->first();
      if($rapport){
        $file=storage_path().'/app/public/'.$rapport->fichier;
        unlink($file);
        $rapport->delete();
        return response([
            'message'=>'rapport supprimé avec succès'
        ],200);

      }else{
        return response([
            'message'=>'rapport introuvable'
        ],404);
      }
    }


    public function update(Request $request, $id){
        $attrs=$request->validate([
            'titre'=>'string|required',
            'description'=>'string'
        ]);
        
        $rapport=RapportPFE::where('id','=',$id)->first();
        if($rapport){
            if($request->has('description')){
                $rapport->update([
                    'titre'=>$attrs['titre'],
                    'description'=>$attrs['description']
                ]);
                return response([
                    'message'=>'rapport mise à jour avec succès.'
                ],200);
            }else{
                $rapport->update([
                    'titre'=>$attrs['titre'],
                    'description'=>null
                ]);
                return response([
                    'message'=>'rapport mise à jour avec succès.'
                ],200);
            }
        }
        else{
            return response([
                'message'=>'rapport inexistant.'
            ],200);
        }
    }


}
