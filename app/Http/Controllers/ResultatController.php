<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Epreuve;
use App\Models\Student;
use App\Models\Resultat;
use Illuminate\Http\Request;
use App\Models\MatiereClasse;
use App\Models\EtudiantClasse;

class ResultatController extends Controller
{
    //
    public function index(){
        $etudiantId=auth('sanctum')->user()->id;
        $resultats=Resultat::where('etudiant_id','=',$etudiantId)
                           ->with(['epreuve'=>function($query){
                                    $query->select('id','matiere_id')
                                          ->with('matiere:id,nom');
                                }])
                           ->get();
        if(count($resultats)>0){
            return response([
                'messaage'=>'voila vos résultats',
                'resultats'=>$resultats
            ],200);
        }
        else{
            return response([
                'messaage'=>'Rien de résultats',
            ],400); 
        }                   
    }


    public function store(Request $request){
        $attrs=$request->validate([
            'matiere_id'=>'required|integer',
            'etudiant_id'=>'required|integer',
            'note'=>'required|numeric|between:00.00,20.00'
        ]);
        $matiereId=$attrs['matiere_id'];
        $etudiantId=$attrs['etudiant_id'];
        $classes=EtudiantClasse::where('student_id','=',$etudiantId)->get('classe_id');
        foreach($classes as $classe){
            $clss=Classe::where('id','=',$classe->classe_id)->first('type_id');
            if($clss){
                $classeId=$classe->classe_id;
            }
        }
        $matiere=MatiereClasse::where('classe_id','=',$classeId)->where('matiere_id','=',$matiereId)->first();
        if($matiere){
            $epreuve=Epreuve::where('classe_id','=',$classeId)->where('matiere_id','=', $matiereId)->first();
            if($epreuve){
                $resultat=Resultat::create([
                    'epreuve_id'=>$epreuve->id,
                    'etudiant_id'=>$etudiantId,
                    'note'=>$attrs['note']
                ]);
                if($resultat){
                   return response([
                    'message'=>'Note ajouté avec succès'
                   ],200);
    
                }else{
                   return response([
                    'message'=>'Oops problème du serveur'
                   ],500);
                }
            }
        }
        else{
            return response([
                'message'=>"Matiere non assigné au classe de l'etudiant"
               ],401); 
        }

    }

    public function update(Request $request, $id){
        $attrs=$request->validate([
            'note'=>'required|numeric|between:00.00,20.00'
        ]);
        $resultat=Resultat::where('id','=',$id)->first();
        if($resultat){
            $resultat->update([
                'note'=>$attrs['note']
            ]);
            return response([
                'message'=>'note mise à jour avec succès'
            ],200);
        }else{
            return response([
                'message'=>'Résultat introuvable'
            ],404);
        }

    }

    public function destroy($id){
        $resultat=Resultat::where('id','=',$id)->first();
        if($resultat){
            $resultat->delete();
            return response([
                'message'=>'Résultat supprimé avec succès'
            ],200);
        }else{
            return response([
                'message'=>'Résultat introuvable'
            ],404);
        }

    }
}
