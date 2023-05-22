<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Enseignant;
use App\Models\Epreuve;
use App\Models\EtudiantClasse;
use App\Models\Matiere;
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
        $epreuves=Epreuve::with('classe:id,nom')->with('enseignant:id,nom,prenom')->with('salle:id,nom')->get();
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
                         ->with([
                                'enseignant' => function ($query) {
                                    $query->select('id','nom', 'prenom');
                                }
                            ])
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
        'matiere_id'=>'required|integer',
        'enseignant_id'=>'required|integer',
        'salle_id'=>'required|integer',
        'date'=>'required|date_format:Y-m-d',
        'startTime'=>'required|date_format:H:i:s',
        'endTime'=>'required|date_format:H:i:s'

       ]);
       $enseignantId=$attrs['enseignant_id'];

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
            $epreuve=Epreuve::create([
                'classe_id'=>$classeId,
                'matiere_id'=>$matiereId,
                'enseignant_id'=>$enseignantId,
                'salle_id'=>$attrs['salle_id'],
                'date'=>$attrs['date'],
                'startTime'=>$attrs['startTime'],
                'endTime'=>$attrs['endTime']
            ]);
            if($epreuve){
                $epreuve->matiere()->associate($matiereId);
                $epreuve->enseignant()->associate($enseignantId);
                $epreuve->classe()->associate($classeId);
                $epreuve->salle()->associate($attrs['salle_id']);

                return response([
                    'message'=>'epreuve créé avec succès'
                ],200);
            }
            else{
                return response([
                    'message'=>'Oops... problème de serveur'
                ],500); 
            }
        } else{
            return response([
                'message'=>'Enseignant non assigné'
            ],422); 
        }      
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
