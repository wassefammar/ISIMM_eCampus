<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Epreuve;
use App\Models\EtudiantClasse;
use App\Models\Matiere;
use App\Models\MatiereClasse;
use App\Models\Salle;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;

class EpreuveController extends Controller
{
    //
/*     public function indexForAdmin(){
        $enseignants=Enseignant::all(['id','nom','prenom']);
        $classes=Classe::all(['id','nom']);
        $matieres=Matiere::all(['id','nom']);
        $salles=Salle::all(['id','nom']);

        return response([
            'classes'=>$classes,
            'matieres'=>$matieres,
            'enseignants'=>$enseignants,
            'salles'=>$salles
        ],200);
      
    } */

    public function indexForAdmin(){
        $epreuves=Epreuve::with('classe:id,nom')->with('salle:id,nom')->get();
        if(count($epreuves)>0){
            return response([
                'message'=>'voilà la liste de tous les examens',
                'epreuves'=>$epreuves
            ]);
        }
        else{
            return response([
                'message'=>"Pas d'examens pour le moment",
            ]); 
        }
    }

    public function indexForStudents(){
        $etudiantId=auth('sanctum')->user()->id;
        $classeIds=EtudiantClasse::where('student_id',"=",$etudiantId)->get('classe_id');
        if(count($classeIds)>0){
            $j=0;
            foreach($classeIds as $classeId){
                $clss[$j]=$classeId->classe_id;
                $j++;
            }
        }


        $epreuves=Epreuve::whereIn('classe_id',$clss)
                         ->with(['matiere' => function ($query) {
                                    $query->select('id','nom');
                                }
                            ])
                            ->with(['salle' => function ($query) {
                                $query->select('id','nom');
                                  }
                            ])
                        ->get();
        if(count($epreuves)>0){
            return response([
                'epreuves'=>$epreuves
            ],200);
        }
        else{
            return response([
                'message'=>'Rien à afficher'
            ],404);
        }                 


    }

    public function store(Request $request){
        $attrs=$request->validate([
            'classe_id'=>'required|integer',
            'epreuves'=>'required|array',
        ]);
        $classeId=$attrs['classe_id'];
        $epreuves=$attrs['epreuves'];
        foreach($epreuves as $epreuve){
            $i=0;
            $j=0;
                        // Add curly braces to the string to make it a valid array representation
            $string = "[$epreuve];";

            // Evaluate the string as PHP code to convert it into an associative array
            eval("\$epreuve = $string");
            $salleId=Salle::where('nom','=',$epreuve['salle_id'])->first();
            $matiereId=Matiere::where('nom','=',$epreuve['matiere_id'])->first();
            $vrai=MatiereClasse::where('matiere_id','=',$matiereId->id)->where('classe_id','=',$classeId)->first();
            if($vrai){
                if($salleId){
                    $exist=Epreuve::create([
                        'matiere_id'=>$matiereId->id,
                        'classe_id'=>$classeId,
                        'salle_id'=>$salleId->id,              
                        'date'=>$epreuve['date'],
                        'startTime'=>$epreuve['startTime'],
                        'endTime'=>$epreuve['endTime']
                     ]);
                     $exist->classe()->associate($classeId);
                     $exist->salle()->associate($epreuve['salle_id']);
                     $exist->matiere()->associate($epreuve['matiere_id']); 
                }
                else{
                    $i++;
                   continue;
                }
  
            }
            else{
                $j++;
                continue;
            }
        
        }
        if($i!=0 || $j!=0){
            return response([
                'message'=>'Peut etre que certaines épreuves ne sont pas ajoutés à cause de faute de salle ou matiere'
           ],200);
        }
        return response([
             'message'=>'tout les épreuves sont ajoutés'
        ],200);
        
    }

    public function update(Request $request, $id){
        $attrs=$request->validate([
            'date'=>'required|date_format:Y-m-d',
            'startTime'=>'required|date_format:H:i:s',
            'endTime'=>'required|date_format:H:i:s'
        ]);
        $epreuve=Epreuve::where('id','=',$id)->first();
        if($epreuve){
            if($attrs['startTime']<$attrs['endTime']){
                $epreuve->update([
                    'date'=>$attrs['date'],
                    'startTime'=>$attrs['startTime'],
                    'endTime'=>$attrs['endTime']
                ]);
                return response([
                    'message'=>'Epreuve mis à jour avec succès'
                ],200);

            }else{
                return response([
                    'message'=>'Vérifier les temps'
                ],200);
            }

        }else{
            return response([
                'message'=>'Epreuve introuvable'
            ],404);
        }

    }

    public function destroy($id){
        $epreuve=Epreuve::where('id','=',$id)->first();
        if($epreuve){
            $epreuve->delete();
            return response([
                'message'=>'Epreuve supprimé avec succès'
            ],200);
        }else{
            return response([
                'message'=>'Epreuve introuvable'
            ],404);
        }

    }
}
