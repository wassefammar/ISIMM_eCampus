<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Epreuve;
use App\Models\Student;
use App\Models\Resultat;
use Illuminate\Http\Request;
use App\Models\MatiereClasse;
use App\Models\EtudiantClasse;

class ResultatController extends Controller
{
    //
    public function index(){
        $etudiantId=auth('sanctum')->user()->id;
        $resultats=Resultat::where('student_id','=',$etudiantId)
                            ->with('matiere:id,nom')
                           ->get();
        if(count($resultats)>0){
            return response([
                'messaage'=>'voila vos résultats',
                'resultats'=>$resultats
            ],200);
        }
        else{
            return response([
                'messaage'=>'Rien de résultats',
            ],404); 
        }                   
    }


    public function store(Request $request){
        $attrs=$request->validate([
            'matiere_id'=>'required|integer',
            'etudiant_id'=>'required|integer',
            'note_TD'=>'numeric|between:00.00,20.00',
            'note_TP'=>'numeric|between:00.00,20.00',
            'note_DS'=>'numeric|between:00.00,20.00',
            'note_Examen'=>'numeric|between:00.00,20.00',
            'moyenne'=>'numeric|between:00.00,20.00',
            'credit'=>'integer'
        ]);
        $matiereId=$attrs['matiere_id'];
        $etudiantId=$attrs['etudiant_id'];
        $etudiant=Student::find($etudiantId);
        if($etudiant){
            $classes=EtudiantClasse::where('student_id','=',$etudiantId)->get('classe_id');
            if(count($classes)>0){
                foreach($classes as $classe){
                    $clss=Classe::where('id','=',$classe->classe_id)->where('type_id','=',1)->first();
                    if($clss){
                        $classeId=$classe->classe_id;
                        break;
                    }
        
                }
        
               $matiere=MatiereClasse::where('classe_id','=',$classeId)->where('matiere_id','=',$matiereId)->first();
                if($matiere){
                    $resultat=Resultat::where('matiere_id','=',$matiereId)->where('student_id','=',$etudiantId)->first();
                    if($resultat){
                        if($request->has('note_TD')){
                            if($request->has('note_TP')){
                                if($request->has('note_DS')){
                                    if($request->has('note_Examen')){
                                        if($request->has('moyenne')){
                                            if($request->has('credit')){
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>$attrs['credit']
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
        
                                            }else{
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
        
                                        }else{
                                            $resultat->update([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>$attrs['note_TD'],
                                                'note_TP'=>$attrs['note_TP'],
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>$attrs['note_Examen'],
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
        
                                    }else{
                                        $resultat->update([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>$attrs['note_DS'],
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                        ]);
                                        return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                    }
        
                                }else{
                                    if($request->has('note_Examen')){
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>$attrs['note_TD'],
                                        'note_TP'=>$attrs['note_TP'],
                                        'note_DS'=>null,
                                        'note_Examen'=>$attrs['note_Examen'],
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
        
                                    }else{
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>$attrs['note_TD'],
                                        'note_TP'=>$attrs['note_TP'],
                                        'note_DS'=>null,
                                        'note_Examen'=>null,
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
                                    }
                                    
                                }
        
                            }else{
                                if($request->has('note_DS')){
                                    if($request->has('note_Examen')){
                                        if($request->has('moyenne')){
                                            if($request->has('credit')){
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>$attrs['credit']
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
        
                                            }else{
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
        
                                        }else{
                                            $resultat->update([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>$attrs['note_TD'],
                                                'note_TP'=>null,
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>$attrs['note_Examen'],
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
        
                                    }else{
                                        $resultat->update([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>null,
                                            'note_DS'=>$attrs['note_DS'],
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                        ]);
                                        return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                    }
        
                                }else{
                                    if($request->has('note_Examen')){
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>$attrs['note_TD'],
                                        'note_TP'=>null,
                                        'note_DS'=>null,
                                        'note_Examen'=>$attrs['note_Examen'],
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
        
                                    }else{
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>$attrs['note_TD'],
                                        'note_TP'=>null,
                                        'note_DS'=>null,
                                        'note_Examen'=>null,
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
                                    }
                                    
                                }
                                
                            }
        
                        }else{
                            if($request->has('note_TP')){
                                if($request->has('note_DS')){
                                    if($request->has('note_Examen')){
                                        if($request->has('moyenne')){
                                            if($request->has('credit')){
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>$attrs['credit']
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
        
                                            }else{
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
        
                                        }else{
                                            $resultat->update([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>null,
                                                'note_TP'=>$attrs['note_TP'],
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>$attrs['note_Examen'],
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
        
                                    }else{
                                        $resultat->update([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>$attrs['note_DS'],
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                        ]);
                                        return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                    }
        
                                }else{
                                    if($request->has('note_Examen')){
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>null,
                                        'note_TP'=>$attrs['note_TP'],
                                        'note_DS'=>null,
                                        'note_Examen'=>$attrs['note_Examen'],
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                      ],200);
        
                                    }else{
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>null,
                                        'note_TP'=>$attrs['note_TP'],
                                        'note_DS'=>null,
                                        'note_Examen'=>null,
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
                                    }
                                    
                                }
        
                            }else{
                                if($request->has('note_DS')){
                                    if($request->has('note_Examen')){
                                        if($request->has('moyenne')){
                                            if($request->has('credit')){
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>$attrs['credit']
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
        
                                            }else{
                                                $resultat->update([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>$attrs['moyenne'],
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
        
                                        }else{
                                            $resultat->update([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>null,
                                                'note_TP'=>null,
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>$attrs['note_Examen'],
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
        
                                    }else{
                                        $resultat->update([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>null,
                                            'note_DS'=>$attrs['note_DS'],
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                        ]);
                                        return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                    }
        
                                }else{
                                    if($request->has('note_Examen')){
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>null,
                                        'note_TP'=>null,
                                        'note_DS'=>null,
                                        'note_Examen'=>$attrs['note_Examen'],
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                    ],200);
        
                                    }else{
                                      $resultat->update([
                                        'matiere_id'=>$matiereId,
                                        'student_id'=>$etudiantId,
                                        'note_TD'=>null,
                                        'note_TP'=>null,
                                        'note_DS'=>null,
                                        'note_Examen'=>null,
                                        'moyenne'=>null,
                                        'credit'=>null
                                      ]);
                                      return response([
                                        'message'=>'Resultat mis à jour avec succès'
                                     ],200);
                                    }
                                    
                                }
                                
                            }
        
                        }
        
        
        
        
        
        
                     
                    }else{
                            if($request->has('note_TD')){
                                if($request->has('note_TP')){
                                    if($request->has('note_DS')){
                                        if($request->has('note_Examen')){
                                            if($request->has('moyenne')){
                                                if($request->has('credit')){
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>$attrs['note_TD'],
                                                        'note_TP'=>$attrs['note_TP'],
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>$attrs['credit']
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
            
                                                }else{
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>$attrs['note_TD'],
                                                        'note_TP'=>$attrs['note_TP'],
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>null
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
                                                }
            
                                            }else{
                                                $resultat=Resultat::create([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>null,
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
            
                                        }else{
                                            $resultat=Resultat::create([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>$attrs['note_TD'],
                                                'note_TP'=>$attrs['note_TP'],
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>null,
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
            
                                    }else{
                                        if($request->has('note_Examen')){
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>null,
                                            'note_Examen'=>$attrs['note_Examen'],
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
            
                                        }else{
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>null,
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                        }
                                        
                                    }
            
                                }else{
                                    if($request->has('note_DS')){
                                        if($request->has('note_Examen')){
                                            if($request->has('moyenne')){
                                                if($request->has('credit')){
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>$attrs['note_TD'],
                                                        'note_TP'=>null,
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>$attrs['credit']
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
            
                                                }else{
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>$attrs['note_TD'],
                                                        'note_TP'=>null,
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>null
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
                                                }
            
                                            }else{
                                                $resultat=Resultat::create([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>$attrs['note_TD'],
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>null,
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
            
                                        }else{
                                            $resultat=Resultat::create([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>$attrs['note_TD'],
                                                'note_TP'=>null,
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>null,
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
            
                                    }else{
                                        if($request->has('note_Examen')){
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>null,
                                            'note_DS'=>null,
                                            'note_Examen'=>$attrs['note_Examen'],
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
            
                                        }else{
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>$attrs['note_TD'],
                                            'note_TP'=>null,
                                            'note_DS'=>null,
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                        }
                                        
                                    }
                                    
                                }
            
                            }else{
                                if($request->has('note_TP')){
                                    if($request->has('note_DS')){
                                        if($request->has('note_Examen')){
                                            if($request->has('moyenne')){
                                                if($request->has('credit')){
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>null,
                                                        'note_TP'=>$attrs['note_TP'],
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>$attrs['credit']
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
            
                                                }else{
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>null,
                                                        'note_TP'=>$attrs['note_TP'],
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>null
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
                                                }
            
                                            }else{
                                                $resultat=Resultat::create([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>$attrs['note_TP'],
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>null,
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
            
                                        }else{
                                            $resultat=Resultat::create([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>null,
                                                'note_TP'=>$attrs['note_TP'],
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>null,
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
            
                                    }else{
                                        if($request->has('note_Examen')){
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>null,
                                            'note_Examen'=>$attrs['note_Examen'],
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                          ],200);
            
                                        }else{
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>$attrs['note_TP'],
                                            'note_DS'=>null,
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
                                        }
                                        
                                    }
            
                                }else{
                                    if($request->has('note_DS')){
                                        if($request->has('note_Examen')){
                                            if($request->has('moyenne')){
                                                if($request->has('credit')){
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>null,
                                                        'note_TP'=>null,
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>$attrs['credit']
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
            
                                                }else{
                                                    $resultat=Resultat::create([
                                                        'matiere_id'=>$matiereId,
                                                        'student_id'=>$etudiantId,
                                                        'note_TD'=>null,
                                                        'note_TP'=>null,
                                                        'note_DS'=>$attrs['note_DS'],
                                                        'note_Examen'=>$attrs['note_Examen'],
                                                        'moyenne'=>$attrs['moyenne'],
                                                        'credit'=>null
                                                    ]);
                                                    return response([
                                                        'message'=>'Resultat mis à jour avec succès'
                                                    ],200);
                                                }
            
                                            }else{
                                                $resultat=Resultat::create([
                                                    'matiere_id'=>$matiereId,
                                                    'student_id'=>$etudiantId,
                                                    'note_TD'=>null,
                                                    'note_TP'=>null,
                                                    'note_DS'=>$attrs['note_DS'],
                                                    'note_Examen'=>$attrs['note_Examen'],
                                                    'moyenne'=>null,
                                                    'credit'=>null
                                                ]);
                                                return response([
                                                    'message'=>'Resultat mis à jour avec succès'
                                                ],200);
                                            }
            
                                        }else{
                                            $resultat=Resultat::create([
                                                'matiere_id'=>$matiereId,
                                                'student_id'=>$etudiantId,
                                                'note_TD'=>null,
                                                'note_TP'=>null,
                                                'note_DS'=>$attrs['note_DS'],
                                                'note_Examen'=>null,
                                                'moyenne'=>null,
                                                'credit'=>null
                                            ]);
                                            return response([
                                                'message'=>'Resultat mis à jour avec succès'
                                            ],200);
                                        }
            
                                    }else{
                                        if($request->has('note_Examen')){
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>null,
                                            'note_DS'=>null,
                                            'note_Examen'=>$attrs['note_Examen'],
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                        ],200);
            
                                        }else{
                                            $resultat=Resultat::create([
                                            'matiere_id'=>$matiereId,
                                            'student_id'=>$etudiantId,
                                            'note_TD'=>null,
                                            'note_TP'=>null,
                                            'note_DS'=>null,
                                            'note_Examen'=>null,
                                            'moyenne'=>null,
                                            'credit'=>null
                                          ]);
                                          return response([
                                            'message'=>'Resultat mis à jour avec succès'
                                         ],200);
                                        }
                                        
                                    }
                                    
                                }
            
                            }
                    
                    }
                }
                else{
                    return response([
                        'message'=>"Matiere non assigné au classe de l'etudiant"
                    ],401); 
                }
            }else{
                return response([
                    'message'=>'étudiant non assigné à aucune classe',
                ],422);
            }

        }
        else{
            return response([
                'message'=>'etudiant non existant',
            ],404);
        }


    }



    public function destroy($id){
        $resultat=Resultat::where('id','=',$id)->first();
        if($resultat){
            $resultat->delete();
            return response([
                'message'=>'Résultat supprimé avec succès'
            ],200);
        }else{
            return response([
                'message'=>'Résultat introuvable'
            ],404);
        }

    }
}