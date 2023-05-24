<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\Student;
use App\Models\Enseignant;
use App\Models\EmploiTemps;
use App\Models\EmploiSeance;
use Illuminate\Http\Request;
use App\Models\MatiereClasse;
use App\Models\EtudiantClasse;
use App\Models\SessionMatiere;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;

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
            'jours'=>'array|required',
        ]);
        $classeId=$attrs['classe_id'];
        $jours=$attrs['jours'];
/*          $s1='matiere_id'.'=>'.'Algebre'.' 2'.','.'enseignant_id'.'=>'.'1'.','.'salle_id'.'=>'.'A22'.','.'day'.'=>'.'lundi'.','.'start_time'.'=>'.'8'.':'.'30'.':'.'22'.','.'end_time'.'=>'.'10'.':'.'20'.':'.'00';
        $s2='matiere_id'.'=>'.'Algebre'.' 2'.','.'enseignant_id'.'=>'.'1'.','.'salle_id'.'=>'.'A22'.','.'day'.'=>'.'lundi'.','.'start_time'.'=>'.'8'.':'.'40'.':'.'22'.','.'end_time'.'=>'.'10'.':'.'30'.':'.'00';
        $s3=$s1.';'.$s2;
        $jours=array();
        $seances=array();
        array_push($jours,$s3); */ 
        //$jours=array();
        //$jours[0]='matiere_id'=>'Algebre 2','enseignant_id'=>1,'salle_id'=>'A22','day'=>'lundi','start_time'=>'8:30:22','end_time'=>'10:20:00';'matiere_id'=>'Algebre 2','enseignant_id'=>1,'salle_id'=>'A22','day'=>'lundi','start_time'=>'8:30:22','end_time'=>'10:20:00';'matiere_id'=>'Algebre 2','enseignant_id'=>1,'salle_id'=>'A22','day'=>'lundi','start_time'=>'8:30:22','end_time'=>'10:20:00'";
        $classe=Classe::find($classeId)->first();
        if($classe){
            $exist=EmploiTemps::where('classe_id','=',$classe->id)->first();
            if($exist){
                foreach($jours as $jour){
                    $i=0;
                    $seances=explode(';',$jour);
                    foreach($seances as $seance){
                        $pairs = explode(",", $seance);

                        // Initialize an empty array
                        $array = array();

                        // Iterate through the pairs and create the associative array
                        foreach ($pairs as $pair) {
                            list($key, $value) = explode("=>", $pair);
                            $array[($key)] = ($value);
                        }
                        print_r($array);
                        print(strval($array['matiere_id']) );
                        print($array['enseignant_id']);
                        print($array['salle_id']);
                        $enseignantId=intval($array['enseignant_id']);
                        $salleId=Salle::where('nom','=',$array['salle_id'])->first();
                        $matiere=Matiere::where('nom','=',strval($array['matiere_id']))->first();
                        $matiereId=$matiere->id;
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
                            $session=SessionMatiere::create([
                                'matiere_id'=>$matiereId,
                                'enseignant_id'=>$enseignantId,
                                'salle_id'=>$salleId->id,
                                'day'=>$array['day'],
                                'startTime'=>$array['start_time'],
                                'endTime'=>$array['end_time']
                            ]);
                            if($session){
                                $exist->seances()->attach($session->id);
                            }
                        }else{
                            $i++;
                            continue;
                        }                        

                    } 
                      

                }
                if($i!=0){
                    //
                    return response([
                        'message'=>"peut etre que l'emploi manque des sèances soyez sur que les enseignants correspendent à cette classe et les matieres correspendent  aux enseignants et à la classe",
                    ],200);
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
                    foreach($jours as $jour){
                        $j=0;
                        $seances=explode(';',$jour);
                        foreach($seances as $seance){
                            $pairs = explode(",", $seance);
    
                            // Initialize an empty array
                            $array = array();
    
                            // Iterate through the pairs and create the associative array
                            foreach ($pairs as $pair) {
                                list($key, $value) = explode("=>", $pair);
                                $array[($key)] = ($value);
                            }
    
                            $enseignantId=intval($array['enseignant_id']);
                            $salleId=Salle::where('nom','=',$array['salle_id'])->first();
                            $matiere=Matiere::where('nom','=',$array['matiere_id'])->first();
                            $matiereId=$matiere->id;
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
                                $session=SessionMatiere::create([
                                    'matiere_id'=>$matiereId,
                                    'enseignant_id'=>$enseignantId,
                                    'salle_id'=>$salleId->id,
                                    'day'=>$array['day'],
                                    'startTime'=>$array['start_time'],
                                    'endTime'=>$array['end_time']
                                ]);
                                if($session){
                                    $emploi->seances()->attach($session->id);
                                }
                            }else{
                                $i++;
                                continue;
                            }                        
    
                        } 
                    }
                    if($j!=0){
                        return response([
                            'message'=>"peut etre que l'emploi mise à jour avec succès"
                        ],200);
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
