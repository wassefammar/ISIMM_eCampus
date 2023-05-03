<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Matiere;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classes=Classe::all();
        if($classes){
            return response([
                'message'=>'Voilà les classes',
                'classes'=>$classes
            ],200);
        }
        else{
            return response([
                'message'=>'pas de classes pour le moment'
            ],200);
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
        $attrs= $request->validate([
            'nom'=>'required|string'
        ]);
        if(Classe::where('nom','=',$attrs['nom'])->exists()){
            return response([
                'message'=>'Classe dèja existante',
            ],409);
        }
        else{
            $classe=Classe::create([
                'nom'=>$attrs['nom']
            ]);
            if($classe){
                $classeId=Classe::where('nom','=',$attrs['nom'])->first('id');
                $chatRoom=ChatRoom::create([
                   'classe_id'=>intval($classeId->id) ,
                   'name'=>$attrs['nom']
                ]);
                if($chatRoom){
                    return response([
                        'message'=>'Classe et chat room crée avec succès',
                        'classe'=> $classe
                    ],200);
                }
                else{
                    return response([
                        'message'=>'Classe crée avec succès',
                        'classe'=> $classe
                    ],200);
                }

            }
            else{
                return response([
                    'message'=>'Oops.. problème'                
                ],500); 
            }

        }

    }

    public function AssignClassToProf(Request $request){
      $attrs=$request->validate([
        'matiere_id'=>'required|integer',
        'classe_id'=>'required|integer',
        'enseignant_id'=>'required|integer'
      ]);
      $matiere=Matiere::find($attrs['matiere_id']);
      $classe=Classe::find($attrs['classe_id']);
      $enseignant=Enseignant::find($attrs['enseignant_id']);
      if ($classe) {
        if ($matiere) {
             if ($enseignant) {
                $enseignant->matieres()->attach($matiere->id);
                $classe->matieres()->attach($matiere->id);
                $classe->enseignants()->attach($enseignant->id);
                return response([
                    'message'=>'Associé avec succés.',
                ],200);

             } else {
                return response([
                    'message'=>'Enseignant non existant',
                ],404);           
              }
             
        } else {
            return response([
                'message'=>'Matiere non existant',
            ],404);  
        }
        
      } else {
        return response([
            'message'=>'Classe non existant',
        ],404);  
      }
      
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $classe=Classe::find($id);
        if(!$classe){
            return response([
                'message'=>'Classe inexistante',
            ],403);
        }
        else{
            $attrs= $request->validate([
                'nom'=>'required|string'
            ]);
            $classe->update([
                'nom'=>$attrs['nom']
            ]);
            
            return response([
                'message'=>'Classe mise à jour',
                'classe'=> $classe
            ],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $classe=Classe::find($id);
        if($classe){
            $classe->delete();
            return response([
                'message'=>'Classe supprimée avec succès'

            ],200);
        }
        else{
            return response([
                'message'=>'Classe inexistante'

            ],200); 
        }
    }
}
