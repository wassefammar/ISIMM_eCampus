<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\EmploiSeance;
use App\Models\EmploiTemps;
use App\Models\EtudiantClasse;
use App\Models\FichePresence;
use App\Models\ListPresence;
use App\Models\Matiere;
use App\Models\SessionMatiere;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;

class ListPresenceController extends Controller
{
    //

    public function index(Request $request){
       $attrs=$request->validate([
        'matiere_id'=>'required|integer',
        'classe_id'=>'required|integer',
       ]);
       $enseignantId=auth('sanctum')->user()->id;
       $classeId=$attrs['classe_id'];
       $matiereId=$attrs['matiere_id'];

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


       // $etudiants=Student::where('classe_id','=',$classeId)->get(['nom','prenom', 'image']);
        $etudiantIds=EtudiantClasse::where('classe_id','=',$classeId)->get('student_id');
        $i=0;
        foreach($etudiantIds as $etudiantId){
            $etudiants[$i]=Student::where('id','=',$etudiantId->student_id)->get(['id','nom','prenom', 'image']);
            $i++;
        }
        if (count($etudiants)>0) {
            if(count($emc)>0){
                return response([
                    'message'=>'Voilà la liste des étudiants.',
                     'étudiants'=>$etudiants
                ],200);
    
            }else{
                return response([
                    'message'=>'Non autorisé à voir la liste des étudiants.',
                ],403);
            }        
        } else {
            return response([
                'message'=>"Cette classe n'a pas des étudiants.",
            ],403);
        }
        
                            
      

    }


    public function store(Request $request){
        $attrs=$request->validate([
            'sessionMatiere_id'=>'required|integer',
            'date'=>'date_format:Y-m-d H:i:s|required',
            'status'=>'required|array'
           ]);
          $status=$attrs['status'];
           $sessionMatiere=SessionMatiere::find($attrs['sessionMatiere_id'])->first();
           if($sessionMatiere){
                    $enseignantId=$sessionMatiere->enseignant_id;
                    $emploiId=$sessionMatiere->classe_id;
                    $emploiId=EmploiSeance::where('session_matiere_id','=',$attrs['sessionMatiere_id'])->first('emploi_temps_id');
                    if($emploiId){
                        $classe=EmploiTemps::where('id','=',$emploiId->emploi_temps_id)->first('classe_id');
                        $classeId=$classe->classe_id;
                    }else{
                        return response([
                            'message'=>'séance non existante'
                        ],404);
                    }

                    $matiereId=$sessionMatiere->matiere_id;

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

                    $classe=Classe::find($classeId);
                    $matiere=Matiere::find($matiereId);
                    $fichePresence=FichePresence::where('matiere_id','=',$matiereId)
                                                    ->where('classe_id','=',$classeId)
                                                    ->where('enseignant_id','=',$enseignantId)
                                                    ->first();
                    $etudiantIds=EtudiantClasse::where('classe_id','=',$classeId)->get('student_id');
                    if(count($etudiantIds)>0){
                        $i=0;
                        foreach($etudiantIds as $etudiantId){
                            $etudiants[$i]=Student::where('id','=',$etudiantId->student_id)->first('id');
                            $i++;
                        }
                        
                    }
                    else{
                        return response([
                            'message'=>'aucun étudiant attribué à cette classe'
                        ],404);
                    }
                   
                    if($fichePresence){ 
                        if(count($emc)>0){
                            if($classe){
                                if($matiere){
                                    if(count($etudiants)==count($status)) {
                                        for($j=0;$j<count($etudiants);$j++){
                                            $listPresence=ListPresence::create([
                                                'fiche_presence'=>$fichePresence->id,
                                                'sessionMatiere_id'=>$attrs['sessionMatiere_id'],
                                                'date'=>$attrs['date'],
                                                'student_id'=>$etudiants[$j]->id,
                                                'status'=>$status[$j]
                                            ]); 
                                        }
    
                                        if($listPresence){
                                        $listPresence->fichePresence()->associate($fichePresence->id);
                                        return response([
                                             'message'=> 'liste de présence ajouté avec succès.'
                                            ],200);
                                        }
                                        else{
                                            return response([
                                                'message'=>'Oops.. il ya un problème'
                                            ],500);
                                        }
                                    }
                                    else{
                                        return response([
                                            'message'=>'Vérifier les inputs'
                                        ],422);
                                    }

                
                                }else{
                                    return response([
                                        'message'=>'Matiere non existante'
                                    ],404);    
                                }
                
                            }else{
                                return response([
                                    'message'=>'Classe non existante'
                                ],404); 
                            }
                        
                
                        } else{
                            return response([
                                'message'=>'Non autorisé à ajouter des listes de présence.'
                            ],401);
                        } 
                    }else{
                        FichePresence::create([
                            'matiere_id'=>$matiereId,
                            'classe_id'=>$classeId,
                            'enseignant_id'=>$enseignantId
                        ]);
                        $NewfichePresence=FichePresence::where('matiere_id','=',$matiereId)
                                                        ->where('classe_id','=',$classeId)
                                                        ->where('enseignant_id','=',$enseignantId)
                                                        ->first();
                        
                        if($NewfichePresence){
                            for($j=0;$j<count($etudiants);$j++){
                                $listPresence=ListPresence::create([
                                    'fiche_presence'=>$NewfichePresence->id,
                                    'sessionMatiere_id'=>$attrs['sessionMatiere_id'],
                                    'date'=>$attrs['date'],
                                    'student_id'=>$etudiants[$j]->id,
                                    'status'=>$status[$j]
                                ]); 
                            }
                            if($listPresence){
                            $listPresence->fichePresence()->associate($NewfichePresence->id);
                            return response([
                                    'message'=>'liste de présence ajouté avec succès.2'
                                ],200);
                            }
                            else{
                                return response([
                                    'message'=>'Oops.. il ya un problème 2'
                                ],500);
                            }
                        }
                        else{
                            return response([
                                'message'=>'Oops.. il ya un problème 3'
                            ],500);
                        }


                    }                                  
        }else{
            return response([
                'message'=>'Séance introuvable'
            ],404);
        }
    
             
    }


    public function update(Request $request){
        $attrs=$request->validate([
            'status'=>'required|array',
            'students'=>'required|array',
            'sessionMatiere_id'=>'required|integer',
            'date'=>'required|date_format:Y-m-d'
        ]);
        $etudiantIds=$attrs['students'];
        $status=$attrs['status'];
        $listPresence=ListPresence::where('sessionMatiere_id','=',$attrs['sessionMatiere_id'])
                                   ->where('date','=',$attrs['date'])
                                   ->get();
        if(count($listPresence)>0){
            $fichePresence=FichePresence::find($listPresence->first()->fiche_presence)->first();
            if($fichePresence->enseignant_id==auth('sanctum')->user()->id){
                for($j=0;$j<count($etudiantIds);$j++){
                    $listPresenceE=ListPresence::where('sessionMatiere_id','=',$attrs['sessionMatiere_id'])
                                               ->where('date','=',$attrs['date'])
                                               ->where('student_id','=',$etudiantIds[$j])->first();
                    if($listPresenceE){
                        $listPresenceE->update([
                            'status'=>$status[$j],
                          ]);
                    }else{
                        return response([
                            'message'=>'etudiant ayant id '.$etudiantIds[$j].' inexistatnt dans cette liste'
                        ],404);  
                    }
                         

                }
                  return response([
                      'message'=>'liste de présence mise à jour avec succès.'
                     ],200);
              }
              else{
                  return response([
                      'message'=>'Non autorisé à modifier cette liste'
                     ],401);
              }
            }
            else{
                return response([
                    'message'=>'liste de présence introuvable'
                   ],404);
            }
            
    }


    public function destroy($id){

        $listPresence=ListPresence::find($id);
        if($listPresence){
            $fichePresence=FichePresence::find($listPresence->fichePresence_id)->get();
            if($fichePresence->enseignant_id==auth('sanctum')->user()->id){
                $listPresence->delete();
                  return response([
                      'message'=>'liste de présence supprimée avec succès.'
                     ],200);
              }
              else{
                  return response([
                      'message'=>'Non autorisé à supprimée cette liste'
                     ],401);
              }
        }
        else{
            return response([
                 'message'=>'liste de présence introuvable'
                ],404);
            }
    }


}



