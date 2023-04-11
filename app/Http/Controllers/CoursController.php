<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Matiere;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\MatiereClasse;
use App\Models\EnseignantClasse;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CoursController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
/*         $attrs=$request->validate([
           'matiere_id'=>'required|integer'
        ]);
        $matiereId=$attrs['matiere_id'];
        $matieres=Matiere::where('id','=',$matiereId)->where('enseignant_id','=',auth('sanctum')->user()->id)->get();
        if($matieres){
            return response([
                'message'=>'voila les matieres',
                'matieres'=>$matieres
            ],200);
        }
        else{
            return response([
                'message'=>'Votre Compte est en train de traitement...'
            ],204);
        } */
        
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
            $cours=$matiere->cours;
            if (count($cours)>0) {
                return response([
                    'message'=>'Voilà la liste des cours',
                    'cours'=>$cours
                ],200);
            } else {
                return response([
                    'message'=>'Cours vide pour le moment'
                ],200);
            }
                                            
        }else{
            return response([
                'message'=>'Non autorisé à voir les cours'
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
            'name'=>'required|string',
            'description'=>'nullable|string',
            'file' => 'required|mimes:pdf|max:2048',
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
     
            $fileName= Str::random(20).".".$attrs['file']->getClientOriginalExtension();
            $fichier=Cours::create([
                'matiere_id'=>$matiereId,
                'titre'=>$attrs['name'],
                'description'=>$attrs['description'],
                'file'=>$fileName
            ]);
            if($fichier){
                Storage::disk('public')->put($fileName, file_get_contents($attrs['file']));
                $fichier->matiere()->associate($matiereId);
                return response([
                    'message'=>'Document ajouté avec succès',
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }   
        }else{
            return response([
                'message'=>'Non autorisé à ajouter des cours'
            ],403);
           }
    }


    public function download(Request $request){
        //PDF file is stored under project/public/download/info.pdf
        $attrs=$request->validate([
        'filename'=>'required|string'
        ]);
        $fullName=$attrs['filename'].'.pdf';  

        if(Cours::where('file','!=',$fullName)->exists()){
                return response([
                    'message'=>'Document non existant',
                ],404);
        }
        else{
            $fichier= storage_path().'/app/public/'. $fullName;
            $headers = array(
                'Content-Type: application/pdf',
                );

            return response()->download($fichier, $fullName, $headers);


        }

   }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function show(Cours $cours)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $attrs=$request->validate([
            'titre'=>'required|string',
            'description'=>'nullable|string',   
        ]);

        $examen=Cours::find($id);
        if ($examen) {
            $examen->update([
                'titre'=>$attrs['titre'],
                'description'=>$attrs['description']
            ]);

            return response([
                'message'=>'examen mis à jour avec succès.',
                'examen'=>$examen
            ]);

        } else {
            return response([
                'message'=>'examen non existant.',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cours  $cours
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
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

        $cours=Cours::find($id);
        
        if($cours){
            if(count($emc)>0){
                //$cours->matiere->dessociate($cours->matiere_id);
                $cours->delete();
                    return response([
                        'message'=>'Cours supprimée avec succès.',
                    ],200);
            }else{
                return response([
                    'message'=>'Non autorisé à supprimer des cours'
                ],403);
            }

         
        }
        else{
            return response([
                'message'=>'Cours non existante.',
            ],404);
        }
    }
}
