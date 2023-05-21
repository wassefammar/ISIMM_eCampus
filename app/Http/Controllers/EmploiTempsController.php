<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\EtudiantClasse;
use App\Models\Student;
use App\Models\EmploiTemps;
use App\Models\EmploiSeance;
use Illuminate\Http\Request;
use App\Models\SessionMatiere;

class EmploiTempsController extends Controller
{
    //

    public function indexForStudents(){
      $etudiantId=auth('sanctum')->user()->id;

      //$classeId=Student::where('id','=',$etudiantId)->first();
      $classes=EtudiantClasse::where('student_id','=',$etudiantId)->get('classe_id');
      foreach($classes as $classe){
          $clss=Classe::where('id','=',$classe->classe_id)->where('type_id','=',1)->first();
          if($clss){
           $classeId=$clss->id;
          }

      }
   
      //$seances=array();
      $seancesLundi=array();
      $seancesMardi=array();
      $seancesMercredi=array();
      $seancesJeudi=array();
      $seancesVendredi=array();
      $seancesSamedi=array();
      $EmploiId=EmploiTemps::where('classe_id','=',$classeId)->first();
      $sessionIds=EmploiSeance::where('emploi_temps_id','=',$EmploiId->id)->get();
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'lundi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
       if($seance!=null){
           array_push($seancesLundi,$seance);      
        } 
      }
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'mardi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
        if($seance!=null){
           array_push($seancesMardi,$seance);      
        }   
      }
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'mercredi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
        if($seance!=null){
            array_push($seancesMercredi,$seance);
        }                           
      }
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'jeudi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
        if($seance!=null){
            array_push($seancesJeudi,$seance);
        }                           
      }
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'vendredi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
        if($seance!=null){
           array_push($seancesVendredi,$seance);
        }
                                                                  
      }
      for($i=0;$i<count($sessionIds);$i++){
        $seance=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->where('day', '=', 'samedi')
                                   ->orderBy('startTime','asc')
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom,prenom')
                                   ->with('matiere:id,nom')
                                   ->first();
        if($seance!=null){
            array_push($seancesSamedi,$seance);
        }
                                 
      }


      if(count($seancesSamedi)>0 ||count($seancesVendredi)>0 ||count($seancesJeudi)>0|| count($seancesMercredi)>0 || count($seancesMardi)>0 || count($seancesLundi)>0){
         return response([
            'message'=>"Voila l'emploi",
            'seancesLundi'=>$seancesLundi,
            'seancesMardi'=>$seancesMardi,
            'seancesMercredi'=>$seancesMercredi,
            'seancesJeudi'=>$seancesJeudi,
            'seancesVendredi'=>$seancesVendredi,
            'seancesSamedi'=>$seancesSamedi
         ],200);
      } else{
        return response([
            'message'=>'emploi vide pour le moment',
         ],404);
      }     

    }

    public function indexForEnseignants(){
        $enseignantId=auth('sanctum')->user()->id;
        $sessions=SessionMatiere::where('enseignant_id','=',$enseignantId)
                                ->whereHas('emploi_temps')
                                ->with('salle:id,nom')
                                ->with(['emploi_temps' => function ($query) {
                                    $query->select('emploi_temps_id', 'classe_id')
                                          ->with(['classe' => function ($query) {
                                              $query->select('id', 'nom');
                                          }]);
                                        }])
                                ->with('matiere:id,nom')
                                ->get();
        if(count($sessions)>0){
            return response([
                'message'=>'Voila votre emploi de temps',
                'seances'=>$sessions

            ],200);
        }
        else{
            return response([
                'message'=>'emploi vide pour le moments'
            ],404);
        }

    }

    public function indexForAdmins(Request $request){
        $attrs=$request->validate([
            'classe_id'=>'required|integer'
        ]);

        $classeId=Classe::where('id','=',$attrs['classe_id'])->first();

        if($classeId){
            $emploi=EmploiTemps::where('classe_id','=',$classeId->id)->first();
            if($emploi){
                $seances=array();
                $sessionIds=EmploiSeance::where('emploi_temps_id','=',$emploi->id)->get();
                for($i=0;$i<count($sessionIds);$i++){
                  $seances[$i]=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                             ->with('salle:id,nom')
                                             ->with('enseignant:id,nom,prenom')
                                             ->with('matiere:id,nom')
                                             ->first();
                }
                if(count($seances)>0){
                   return response([
                      'message'=>"Voila l'emploi",
                      'seances'=>$seances
          
                   ],200);
                } else{
                  return response([
                      'message'=>'emploi vide pour le moment',
                   ],404);
                }   
            }else{
                return response([
                    'message'=>'emploi non existant',
                 ],404);
            }
        }
        return response([
            'message'=>'classe non existant',
         ],404);
    }


    public function store(Request $request){
        $attrs=$request->validate([
            'classe_id'=>'required|integer',
            'seances'=>'array|required',
        ]);
        $classeId=$attrs['classe_id'];
        $seances=$attrs['seances'];

        $classe=Classe::find($classeId)->first();
        if($classe){
            $exist=EmploiTemps::where('classe_id','=',$classe->id)->first();
            if($exist){
                foreach($seances as $seance){
                    // Add curly braces to the string to make it a valid array representation
                     $string = "[$seance];";
                   // Evaluate the string as PHP code to convert it into an associative array
                     eval("\$seance = $string");
                    $session=SessionMatiere::create([
                        'matiere_id'=>$seance['matiere_id'],
                        'enseignant_id'=>$seance['enseignant_id'],
                        'salle_id'=>$seance['salle_id'],
                        'day'=>$seance['day'],
                        'startTime'=>$seance['start_time'],
                        'endTime'=>$seance['end_time']
                       ]);
                    if($session){
                        $exist->seances()->attach($session->id);
                    }
                }

                return response([
                    'message'=>'emploi mise à jour avec succès'
                ],200);
            }else{
                $emploi=EmploiTemps::create([
                    'classe_id'=>$classeId,
                    'nom'=>$classe->nom
                ]);
                if($emploi){
                    foreach($seances as $seance){
                                    // Add curly braces to the string to make it a valid array representation
                            $string = "[$seance];";
                
                            // Evaluate the string as PHP code to convert it into an associative array
                            eval("\$seance = $string");
                        $session=SessionMatiere::create([
                            'matiere_id'=>$seance['matiere_id'],
                            'enseignant_id'=>$seance['enseignant_id'],
                            'salle_id'=>$seance['salle_id'],
                            'day'=>$seance['day'],
                            'startTime'=>$seance['start_time'],
                            'endTime'=>$seance['end_time']
                        ]);
                        if($session){
                            $emploi->seances()->attach($session->id);
                        }
                    }
    
                    return response([
                        'message'=>'emploi créé avec succès'
                    ],200);
                }
                else{
                    return response([
                        'message'=>'erreur de emploi'. $classeId
                    ],500);
                }
            }


        }
        else{
            return response([
                'message'=>'classe inexistante'
            ],404);
        }
    }
    
    

    public function destroy($id){
        $emploi=EmploiTemps::where('id','=',$id)->first();

        if($emploi){
            $sessionIds=EmploiSeance::where('emploi_temps_id','=',$emploi->id)->get();
            for($i=0;$i<count($sessionIds);$i++){
                $session=SessionMatiere::find($sessionIds[$i])->first();
                $session->delete();
            }


            $emploi->delete();

            return response([
                'message'=>'emploi supprimé avec succès.'
            ],200);
        }

        else{
            return response([
                'message'=>'emploi inexistatnt'
            ],200);
        }
    }

}
