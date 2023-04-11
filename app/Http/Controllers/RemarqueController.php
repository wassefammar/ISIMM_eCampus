<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Remarque;
use Illuminate\Http\Request;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;

class RemarqueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {
        //
        $attrs=$request->validate([
            'matiere_id'=>'required|integer',
            'classe_id'=>'required|integer'
        ]);
        $enseignantId=auth('sanctum')->user()->id;
        $matiereId=$attrs['matiere_id'];
        $classeId=$attrs['classe_id'];
        $emc = EnseignantMatiere::where('enseignant_id', '=', $enseignantId)
                ->where('matiere_id', '=', $matiereId)
                ->whereExists(function ($query) use ($enseignantId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('enseignant_classes')
                        ->whereRaw('enseignant_classes.enseignant_id = enseignant_matieres.enseignant_id')
                        ->where('enseignant_classes.classe_id', '=', $classeId);
                })
                ->whereExists(function ($query) use ($matiereId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('matiere_classes')
                        ->whereRaw('matiere_classes.matiere_id = enseignant_matieres.matiere_id')
                        ->where('matiere_classes.classe_id', '=', $classeId);
                })
                ->get();

       if(count($emc)>0){
        $matiere=Matiere::find($matiereId);
        $remarques=$matiere->remarques;
        if (count($remarques)>0) {
            return response([
                'message'=>'Voilà la liste des remarques',
                'remarques'=>$remarques
            ],200);
        } else {
            return response([
                'message'=>'Remarques vide pour le moment'
            ],200);
        }
                                        
       }else{
        return response([
            'message'=>'Non autorisé à voir les remarques'
        ],401);
       }
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $attrs=$request->validate([
            'titre'=>'required|string',
            'description'=>'nullable|string',
            'matiere_id'=>'required|integer',
            'classe_id'=>'required|integer'     
        ]);
        $enseignantId=auth('sanctum')->user()->id;
        $matiereId=$attrs['matiere_id'];
        $classeId=$attrs['classe_id'];

        $emc = EnseignantMatiere::where('enseignant_id', '=', $enseignantId)
                ->where('matiere_id', '=', $matiereId)
                ->whereExists(function ($query) use ($enseignantId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('enseignant_classes')
                        ->whereRaw('enseignant_classes.enseignant_id = enseignant_matieres.enseignant_id')
                        ->where('enseignant_classes.classe_id', '=', $classeId);
                })
                ->whereExists(function ($query) use ($matiereId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('matiere_classes')
                        ->whereRaw('matiere_classes.matiere_id = enseignant_matieres.matiere_id')
                        ->where('matiere_classes.classe_id', '=', $classeId);
                })
                ->get();

        if(count($emc)>0){
            $remarque=Remarque::create([
                'matiere_id'=>$matiereId,
                'titre'=>$attrs['titre'],
                'description'=>$attrs['description']
            ]);
            if($remarque){
                $remarque->matiere()->associate($matiereId);
                return response([
                    'message'=>'Remarque ajouté avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }   
        }else{
            return response([
                'message'=>'Non autorisé à ajouter des remarques'
            ],401);
        }
    }





    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Remarque  $remarque
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $attrs=$request->validate([
            'titre'=>'required|string',
            'description'=>'nullable|string',   
        ]);

        $remarque=Remarque::find($id);
        if ($remarque) {
            $remarque->update([
                'titre'=>$attrs['titre'],
                'description'=>$attrs['description']
            ]);

            return response([
                'message'=>'remarque mis à jour avec succès.',
                'remarque'=>$remarque
            ]);

        } else {
            return response([
                'message'=>'exercice non existant.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Remarque  $remarque
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        //
        $attrs=$request->validate([
            'matiere_id'=>'required|integer',
            'classe_id'=>'required|integer'
        ]);

        $enseignantId=auth('sanctum')->user()->id;
        $matiereId=$attrs['matiere_id'];
        $classeId=$attrs['classe_id'];

        $emc = EnseignantMatiere::where('enseignant_id', '=', $enseignantId)
                ->where('matiere_id', '=', $matiereId)
                ->whereExists(function ($query) use ($enseignantId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('enseignant_classes')
                        ->whereRaw('enseignant_classes.enseignant_id = enseignant_matieres.enseignant_id')
                        ->where('enseignant_classes.classe_id', '=', $classeId);
                })
                ->whereExists(function ($query) use ($matiereId, $classeId) {
                    $query->select(DB::raw(1))
                        ->from('matiere_classes')
                        ->whereRaw('matiere_classes.matiere_id = enseignant_matieres.matiere_id')
                        ->where('matiere_classes.classe_id', '=', $classeId);
                })
                ->get();

        $remarque=Remarque::find($id);
        
        if($remarque){
            if(count($emc)>0){
                $remarque->delete();
                return response([
                        'message'=>'Remarque supprimé avec succès.',
                    ],200);
                 
            }else{
                return response([
                    'message'=>'Non autorisé à supprimer des remarques'
                ],403);
            }       
        }
        else{
            return response([
                'message'=>'Remarque non existante.',
            ],404);
        }
    }
}
