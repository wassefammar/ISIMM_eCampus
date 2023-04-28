<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\SessionMatiere;
use Illuminate\Http\Request;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;

class SessionMatiereController extends Controller
{
    //

    public function index(Request $request){
        $attrs=$request->validate([
            'matiere_id'=>'required|integer'
        ]);
        if(Matiere::find($attrs['matiere_id'])){
            $sessions=SessionMatiere::where('matiere_id','=',$attrs['matiere_id'])->get();
            if(count($sessions)>0){
                return response([
                    'message'=>'Voila les horaires de cette matiere',
                    'séances'=>$sessions
                ],200);
            }
            else{
                return response([
                    'message'=>"Cette matiere n'a pas encores des horaires",
                ],404);
            }
        }
        else{
            return response([
                'message'=>'Matiere inexistante',
            ],404);
        }
        


    }


    public function store(Request $request){
        $attrs=$request->validate([
            'matiere_id'=>'required|integer',
            'classe_id'=>'required|integer',
            'enseignant_id'=>'required|integer',
            'start_time'=>'required|date_format:H:i:s',
            'end_time'=>'required|date_format:H:i:s'
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
            if($attrs['start_time']<$attrs['end_time']){
                $sessionMatiere=SessionMatiere::create([
                    'matiere_id'=>$matiereId,
                    'classe_id'=>$classeId,
                    'enseignant_id'=>$enseignantId,
                    'startTime'=>$attrs['start_time'],
                    'endTime'=>$attrs['end_time']
    
                ]);
    
                if($sessionMatiere){
                    return response([
                        'message'=>'Séance créée avec succès.',
                        'séance'=>$sessionMatiere
                    ],200);
                }
                else{
                    return response([
                        'message'=>'Oops... problème'
                    ],500);
                }
            }else{
                return response([
                    'message'=>'il faut que la date de début est inférieur à la date de fin'
                ],500);
            }



        }else{
            return response([
                'message'=>'il faut que cet enseignant soit assigné à cette classe et cette matière'
            ],401);
        }                    


    }
}
