<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Exercices;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExercicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
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
        $exercices=$matiere->exercices;
        if (count($exercices)>0) {
            return response([
                'message'=>'Voilà la liste des exercices',
                'exercices'=>$exercices
            ],200);
        } else {
            return response([
                'message'=>'Exercices vide pour le moment'
            ],200);
        }
                                        
       }else{
        return response([
            'message'=>'Non autorisé à voir les exercices'
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
            $fichier=Exercices::create([
                'matiere_id'=>$matiereId,
                'titre'=>$attrs['titre'],
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
                'message'=>'Non autorisé à ajouter des exercices'
            ],401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Exercices  $exercices
     * @return \Illuminate\Http\Response
     */


    public function download(Request $request){
        //PDF file is stored under project/public/download/info.pdf
        $attrs=$request->validate([
        'filename'=>'required|string'
        ]);
        $fullName=$attrs['filename'].'.pdf';  

        if(Exercices::where('file','!=',$fullName)->exists()){
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Exercices  $exercices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $attrs=$request->validate([
            'titre'=>'required|string',
            'description'=>'nullable|string',   
        ]);

        $exercice=Exercices::find($id);
        if ($exercice) {
            $exercice->update([
                'titre'=>$attrs['titre'],
                'description'=>$attrs['description']
            ]);

            return response([
                'message'=>'exercice mis à jour avec succès.',
                'exercice'=>$exercice
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
     * @param  \App\Models\Exercices  $exercices
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

        $exercice=Exercices::find($id);
        
        if($exercice){
            if(count($emc)>0){
                //$cours->matiere->dessociate($cours->matiere_id);
/*                 $path=storage_path().'/app/public/'.$exercice->titre;
                if(Storage::exists($path))
                 {
                    Storage::delete($path);} */
                $exercice->delete();
                return response([
                        'message'=>'Exercice supprimé avec succès.',
                    ],200);
                 
            }else{
                return response([
                    'message'=>'Non autorisé à supprimer des exercices'
                ],403);
            }       
        }
        else{
            return response([
                'message'=>'Exercice non existante.',
            ],404);
        }
    }
}
