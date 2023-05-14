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
   
      $seances=array();
      $EmploiId=EmploiTemps::where('classe_id','=',$classeId)->first();
      $sessionIds=EmploiSeance::where('emploi_temps_id','=',$EmploiId->id)->get();
      for($i=0;$i<count($sessionIds);$i++){
        $seances[$i]=SessionMatiere::where('id','=',$sessionIds[$i]->session_matiere_id)
                                   ->with('salle:id,nom')
                                   ->with('enseignant:id,nom')
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

    }

    public function indexForEnseignants(){
        $enseignantId=auth('sanctum')->user()->id;
        $sessions=SessionMatiere::where('enseignant_id','=',$enseignantId)
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


    public function store(Request $request){
        $attrs=$request->validate([
            'classe_id'=>'required|integer',
          //  'seances'=>'array|required',
        ]);

       // $seances=$attrs['seances'];
       $s1=['matiere_id'=>1,'enseignant_id'=>1,'salle_id'=>1,'day'=>'Tuesday','start_time'=>'10:25:33', 'end_time'=>'10:53:00'];
       $s2=['matiere_id'=>1,'enseignant_id'=>1,'salle_id'=>1,'day'=>'Monday','start_time'=>'10:25:33', 'end_time'=>'10:53:00'];
       $seances=[$s1,$s2];
        $classeId=$attrs['classe_id'];
        $classe=Classe::find($classeId)->first();
        if($classe){
            $emploi=EmploiTemps::create([
                'classe_id'=>$classeId,
                'nom'=>$classe->nom
            ]);
            if($emploi){
                for($i=0;$i<count($seances);$i++){
                    $seance=SessionMatiere::create([
                        'matiere_id'=>$seances[$i]['matiere_id'],
                        'enseignant_id'=>$seances[$i]["enseignant_id"],
                        'salle_id'=>$seances[$i]['salle_id'],
                        'day'=>$seances[$i]["day"],
                        'startTime'=>$seances[$i]["start_time"],
                        'endTime'=>$seances[$i]["end_time"]
                    ]);
                    if($seance){
                        $emploi->seances()->attach($seance->id);
                    }
                    else{
                        return response([
                            'message'=>'erreur de matiere'
                        ],500);

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