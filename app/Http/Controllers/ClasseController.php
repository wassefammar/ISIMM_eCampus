<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\EnseignantClasse;
use App\Models\EtudiantClasse;
use App\Models\Matiere;
use App\Models\MatiereClasse;
use App\Models\Student;
use App\Models\TypeClasse;
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
        $classes=Classe::with('type:id,type')->withCount('etudiants')->get();
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
            'nom'=>'required|string',
            'type_classe'=>'string|required'
        ]);
        $typeClasse=TypeClasse::where('type','=',$attrs['type_classe'])->first('id');
        if($typeClasse){
            $classee=Classe::where('nom','=',$attrs['nom'])->where('type_id','=',$typeClasse->id)->first();

            if($classee){
                return response([
                    'message'=>'Classe dèja existante',
                ],409);
            }
            else{
                $classe=Classe::create([
                    'nom'=>$attrs['nom'],
                    'type_id'=>$typeClasse->id
                ]);
                if($classe){
                        $classeId=Classe::where('nom','=',$attrs['nom'])->where('type_id','=',$typeClasse->id)->first('id');
                        $chatRoom=ChatRoom::create([
                           'classe_id'=>intval($classeId->id) ,
                           'name'=>$attrs['nom']
                        ]);
                        if($chatRoom){
                            return response([
                                'message'=>'Classe et chat room crée avec succès',
                                'classe'=> $classe,
                                'chatRoom'=>$chatRoom
                            ],200);
                        }
                        
                        else{
                            return response([
                                'message'=>'Oops problème',
                            ],500);
                        }
                }
                else{
                    return response([
                         'message'=>'Oops problème',
                    ],500);
                }
             } 
        }
        else{
           return response([
               'message'=>'type inexistant'
           ],404);
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
                if(EnseignantClasse::where('enseignant_id','=',$enseignant->id)->where('classe_id','=',$classe->id)->exists()
                && MatiereClasse::where('matiere_id','=',$matiere->id)->where('classe_id','=',$classe->id)->exists()){
                    return response([
                        'message'=>'Dèja associé',
                    ],200);
                }
                else{
                    $enseignant->matieres()->attach($matiere->id);
                    $classe->matieres()->attach($matiere->id);
                    $classe->enseignants()->attach($enseignant->id);
                    return response([
                        'message'=>'Associé avec succés.',
                    ],200);
                }


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


    public function desassocierEnseignantClasse(Request $request){
        $attrs=$request->validate([
            'enseignant_id'=>'required|integer',
            'classe_id'=>'required|integer'
        ]);

        $enseignant=Enseignant::where('id','=',$attrs['enseignant_id'])->first('id');
        $classe=Classe::where('id','=',$attrs['classe_id'])->first('id');
        if($enseignant){
            if($classe){
                
                $relation=EnseignantClasse::where('enseignant_id','=',$attrs['enseignant_id'])->where('classe_id','=',$attrs['classe_id'])->exists();
                if($relation){
                    $enseignant->classes()->detach($classe->id);
                    return response([
                        'message'=>'désassocié avec succès'
                      ],404);
                }
                else{
                    return response([
                       'message'=>'déja non associé'
                    ],200);
                }

            }else {
              return response([
                'message'=>'classe introuvable'
              ],404);
            }     
        }
        else{
            return response([
                'message'=>'Enseignant inexistant'
              ],404);
        }

    } 



    public function AssignStudentToClass(Request $request){
        $attrs=$request->validate([
            'etudiant_id'=>'required|integer',
            'classe_id'=>'required|integer'
        ]);
        $etudiant=Student::where('id','=',$attrs['etudiant_id'])->first('id');
        $classe=Classe::where('id','=',$attrs['classe_id'])->first('id');
        if($etudiant){
            if($classe){
                
                $relation=EtudiantClasse::where('student_id','=',$attrs['etudiant_id'])->where('classe_id','=',$attrs['classe_id'])->exists();
                if($relation){
                    return response([
                        'message'=>'déja associé'
                      ],404);
                }
                else{
                    if(count(EtudiantClasse::where('student_id','=',$attrs['etudiant_id'])->get())==3){
                        return response([
                            'message'=>"l'étudiant a atteint le maximum de classes"
                        ],422);
                    }
                    else{
                        $etudiant->classe()->attach($classe->id);
                        return response([
                           'message'=>'associé avec succès'
                         ],200);
                    }

                }

            }else {
              return response([
                'message'=>'classe introuvable'
              ],404);
            }
        
        }
        else{
            return response([
                'message'=>'Etudiant inexistant'
              ],404);
        }

    }


    public function desassocierEtudiantClasse(Request $request){
        $attrs=$request->validate([
            'etudiant_id'=>'required|integer',
            'classe_id'=>'required|integer'
        ]);

        $etudiant=Student::where('id','=',$attrs['etudiant_id'])->first('id');
        $classe=Classe::where('id','=',$attrs['classe_id'])->first('id');
        if($etudiant){
            if($classe){
                
                $relation=EtudiantClasse::where('student_id','=',$attrs['etudiant_id'])->where('classe_id','=',$attrs['classe_id'])->exists();
                if($relation){
                    $etudiant->classe()->detach($classe->id);
                    return response([
                        'message'=>'désassocié avec succès'
                      ],404);
                }
                else{
                    return response([
                       'message'=>'déja non associé'
                    ],200);
                    

                }

            }else {
              return response([
                'message'=>'classe introuvable'
              ],404);
            }
        
        }
        else{
            return response([
                'message'=>'Etudiant inexistant'
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
            ],404); 
        }
    }
}
