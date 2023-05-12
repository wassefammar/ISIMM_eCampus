<?php

namespace App\Http\Controllers;

use App\Models\PFEBook;
use App\Models\Societe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PFEBookController extends Controller
{
    //
    public function indexAjout(){
        $societes=Societe::all('nom')->sortDesc();
        if($societes){
            return response([
                'message'=>'Voilà les societes',
                'societes'=>$societes
            ],200);
        }
        else{
            return response([
                'message'=>'pas de sociétes pour le moment'
            ],404);
        }
    }


    public function index(){
        $PFE_Book=PFEBook::with('societe:id,nom')->orderBy('updated_at','desc')->get();
        if(count($PFE_Book)>0){
            return response([
                'message'=>'voilà les PFE Book',
                'rapports'=>$PFE_Book
            ],200);
        }
        else{
            return response([
                'message'=>'pas de PFE Book pour le moment',
            ],404); 
        }
    }


    public function store(Request $request){
        $attrs=$request->validate([
            'titre'=>'string|required',
            'description'=>'string',
            'societe'=>'string|required',
            'rapport'=>'required|file'
        ]);
        $fileName= Str::random(20).".".$attrs['rapport']->getClientOriginalExtension();
        $societe=Societe::where('nom','=',$attrs['societe'])->first('id');
        if($societe){
            if($request->has('description')){
                $fichier=PFEBook::create([
                    'titre'=>$attrs['titre'],
                    'description'=>$attrs['description'],
                    'societe_id'=>$societe->id,
                    'fichier'=>$fileName
                ]);
                if($fichier){
                    Storage::disk('public')->put($fileName, file_get_contents($attrs['rapport']));
                    return response([
                        'message'=>'PFE Book ajouté avec succès',
                    ],200);
                }else{
                    return response([
                        'message'=>'Oops.. il ya un problème',
                    ],500);
                }
            }
            else{
                $fichier=PFEBook::create([
                    'titre'=>$attrs['titre'],
                    'description'=>null,
                    'societe_id'=>$societe->id,
                    'fichier'=>$fileName
                ]);
                return response([
                    'message'=>'PFE Book ajouté avec succès',
                ],200);
            }
    
            if($fichier){
                Storage::disk('public')->put($fileName, file_get_contents($attrs['rapport']));
                return response([
                    'message'=>'pfe book ajouté avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }
        }
        return response([
            'message'=>'Societe inexistante',
        ],404);

    }

    public function download(Request $request){
                //PDF file is stored under project/public/download/info.pdf
        $attrs=$request->validate([
            'filename'=>'required|string'
        ]);
        $fullName=$attrs['filename'].'.pdf';       
        if(PFEBook::where('fichier','=',$fullName)->exists()){
            $fichier= storage_path().'/app/public/'. $fullName;
            if($fichier){
                $headers = array(
                    'Content-Type: application/pdf',
                );
    
                return response()->download($fichier, $fullName, $headers);
            }
            else{
                return response([
                    'message'=>'fichier non existant au sein du serveur'
                ],404);
            }


        }
        else{
            return response([
                'message'=>'Document non existant',
            ],404);


        }

    }


    public function destroy($id){
      $rapport=PFEBook::where('id','=',$id)->first();
      if($rapport){
        unlink(storage_path().'/app/public/'.$rapport->fichier);
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
        
        $rapport=PFEBook::where('id','=',$id)->first();
        if($rapport){
            if($request->has('description')){
                $rapport->update([
                    'titre'=>$attrs['titre'],
                    'description'=>$attrs['description']
                ]);
                return response([
                    'message'=>'PFE Book mise à jour avec succès.'
                ],200);
            }else{
                $rapport->update([
                    'titre'=>$attrs['titre'],
                    'description'=>null
                ]);
                return response([
                    'message'=>'PFE Book mise à jour avec succès.'
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
