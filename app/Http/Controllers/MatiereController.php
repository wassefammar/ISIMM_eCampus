<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use App\Models\MatiereClasse;
use App\Models\EnseignantMatiere;

class MatiereController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $id=auth('sanctum')->guard('admins')->user()->id;
        $matieres=Matiere::all();
        if(count($matieres)>0){
            return response([
                'message'=>'Tous les matieres',
                'matieres'=>$matieres
            ],200);
        }
        elseif(count($matieres)==0){
            return response([
                'message'=>'Les matieres sont en cours de traitement'
            ],200);
        }
        else{
            return response([
                'message'=>'Oops...il y a un problème'
            ],500);   
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
            'nom'=>'required|string'
        ]);
        $exist=Matiere::where('nom','=',$attrs['nom'])->exists();
        if($exist){
            return response([
                'message'=>'Matière déja existante.',
            ],409); 
        }
        else{
            $matiere=Matiere::create([
                'nom'=>$attrs['nom']
            ]);
            if($matiere){
                    return response([
                        'message'=>'Matière créée avec succès.',
                        'matiere'=>$matiere
                    ],200);
           
            }
            else{
                    return response([
                        'message'=>'Oops...il y a un problème'
                    ],500);   
    
            }
        }
    
        
    }




    public function AssignMatiereToClass(Request $request){
        $attrs=$request->validate([
          'matiere_id'=>'required|integer',
          'classe_id'=>'required|integer'
        ]);
        $matiere=Matiere::find($attrs['matiere_id']);
        $classe=Classe::find($attrs['classe_id']);
        if ($classe) {
          if ($matiere) {
                  $classe->matieres()->attach($matiere->id);
                  return response([
                      'message'=>'Matière associée avec succés.',
                  ],200);
  
            } else {
                    return response([
                        'message'=>'Matiere non existant',
                    ],404);           
                  }
               
        } else {
          return response([
              'message'=>'Classe non existante',
          ],404);  
        }
        
      }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function show(Matiere $matiere)
    {
        //
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $attrs=$request->validate([
            'nom'=>'required|string'
        ]);
        $matiere=Matiere::find($id);
        
        if($matiere){
            if(Matiere::where('nom','=',$attrs['nom']->exists())){
                return response([
                    'message'=>'Matière déja existante.',
                ],409);
            }else{
                $matiere->update([
                    'nom'=>$attrs['nom']
                ]);

                return response([
                    'message'=>'Matière mise à jour avec succès.',
                    'matiere'=>$matiere
                ],200);
            }

         
        }
        else{
            return response([
                'message'=>'Matière non existante.',
            ],200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Matiere  $matiere
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        
        $matiere=Matiere::find($id);
        
        if($matiere){
            $enseignantId=EnseignantMatiere::where('matiere_id','=',$id)->get();
            $classeId=MatiereClasse::where('matiere_id','=',$id)->get();
            $matiere->enseignants->detach($enseignantId->id);
            $matiere->classes->detach($classeId->id);
            $matiere->delete();
                return response([
                    'message'=>'Matière supprimée avec succès.',
                ],200);
         
        }
        else{
            return response([
                'message'=>'Matière non existante.',
            ],404);
        }
    }
}
