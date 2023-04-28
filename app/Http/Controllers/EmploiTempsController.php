<?php

namespace App\Http\Controllers;

use App\Models\Classe;
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

      $classeId=Student::where('id','=',$etudiantId)->first();
      $EmploiId=EmploiTemps::where('classe_id','=',$classeId->classe_id)->first();
      $sessionIds=EmploiSeance::where('emploi_temps_id','=',$EmploiId->id)->get();
      for($i=0;$i<count($sessionIds);$i++){
        $seances[$i]=SessionMatiere::where('id','=',$sessionIds[$i]['session_matiere_id'])->first();
      }
      if(count($seances)>0){
         return response([
            'message'=>'Voila le emploi',
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
        $sessions=SessionMatiere::where('enseignant_id','=',$enseignantId)->get();
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
            'nom'=>'required|string'
        ]);

       // $seances=$attrs['seances'];
       $s1=['matiere_id'=>1,'enseignant_id'=>1,'day'=>'Tuesday','start_time'=>'10:25:33', 'end_time'=>'10:53:00'];
       $s2=['matiere_id'=>1,'enseignant_id'=>1,'day'=>'Monday','start_time'=>'10:25:33', 'end_time'=>'10:53:00'];
       $seances=[$s1,$s2];
        $classeId=$attrs['classe_id'];
        if(Classe::find($classeId)){
            $emploi=EmploiTemps::create([
                'classe_id'=>$classeId,
                'nom'=>$attrs['nom']
            ]);
            if($emploi){
                for($i=0;$i<count($seances);$i++){
                    $seance=SessionMatiere::create([
                        'matiere_id'=>$seances[$i]['matiere_id'],
                        'enseignant_id'=>$seances[$i]["enseignant_id"],
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
