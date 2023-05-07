<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Classe;
use App\Models\Examen;
use App\Models\Student;
use App\Models\Exercices;
use Illuminate\Http\Request;
use App\Models\ClassDocument;
use App\Models\EtudiantClasse;
use App\Models\EnseignantMatiere;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClassDocumentController extends Controller
{

    public function indexEnseignant(Request $request){
         $attrs=$request->validate([
             'matiere_id'=>'required|integer',
             'classe_id'=>'required|integer',
         ]);

         $enseignantId=auth('sanctum')->user()->id;
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
            $students=EtudiantClasse::where('classe_id','=',$classeId)->get('student_id');
            if(count($students)>0){
                $i=0;
                foreach($students as $student){
                    $studentIds[$i]=$student->student_id;
                    $i++;
                }
                
                $classDocuments=ClassDocument::where('matiere_id','=',$matiereId)->whereIn('student_id',$studentIds)->get();
                if(count($classDocuments)>0){
                    return response([
                        'message'=>'Voilà la liste des documents en attente',
                        'Classe Documents'=>$classDocuments
                    ],200);
                }
                else{
                    return response([
                        'message'=>'Rien à afficher'
                    ],200);
                }
            }  else{
                return response([
                    'message'=>'Rien à afficher 2'
                ],200);
            }
        }
        else{
            return response([
                'message'=>'Non autorisé à consulter la liste des documents de cette classe'
            ],401);
        }         
 
    }


    public function indexEtudiant(Request $request){
        $attrs=$request->validate([
            'matiere_id'=>'required|integer'
        ]);
        $etudiantId=auth('sanctum')->user()->id;
        $matiereId=$attrs['matiere_id'];
        $classDocuments=ClassDocument::where('matiere_id','=',$matiereId)->where('student_id','=',$etudiantId)->get();
        if(count($classDocuments)>0){
            return response([
                'message'=>'Volia vos documents en attente',
                'Class Documents'=>$classDocuments
            ],200);
        }
        else{
            return response([
                'message'=>'Rien a afficher'
            ],200);
        }

    }

    public function store(Request $request)
    {
        //
        $attrs=$request->validate([
            'flag'=>'required|string',
            'matiere_id'=>'required|integer',
            'name'=>'required|string',
            'description'=>'nullable|string',
            'file' => 'required|mimes:pdf'     
        ]);
        $studentId=auth('sanctum')->user()->id;
        $student=Student::where('id','=',$studentId)->get();
        if(count($student)>0){            
            $fileName= $attrs['name'].".".$attrs['file']->getClientOriginalExtension();
            $fichier=ClassDocument ::create([
                'matiere_id'=>$attrs['matiere_id'],
                'student_id'=>$studentId,
                'flag'=>$attrs['flag'],
                'titre'=>$attrs['name'],
                'description'=>$attrs['description'],
                'file'=>$fileName
            ]);
            if($fichier){
                Storage::disk('public')->put($fileName, file_get_contents($attrs['file']));
                $fichier->matiere()->associate($attrs['matiere_id']);
                $fichier->etudiant()->associate($studentId);
                return response([
                    'message'=>"Document ajouté avec succès dans la liste d'attente.",
                ],200);
            }else{
                return response([
                    'message'=>'Oops.. il ya un problème',
                ],500);
            }
        }

        else{
            return response([
                'message'=>'Etudiant non existant',
            ],404); 
        }
    }
    public function download(Request $request){
                //PDF file is stored under project/public/download/info.pdf
        $attrs=$request->validate([
            'filename'=>'required|string'
        ]);
        $fullName=$attrs['filename'].'.pdf';       
        if(ClassDocument::where('file','!=',$fullName)->exists()){
            return response([
                'message'=>'Document non existant',
            ],404);
        }
        else{
            $fichier= storage_path().'/app/public/'. $fullName;
            $headers = array(
                'Content-Type: application/pdf',
            );

            return response()->download($fichier, $fullName, $headers);


        }

    }

    public function destroy($id){
        
        $document=ClassDocument::where('id','=',$id)->first();;
        $etudiantId=auth('sanctum')->user()->id;
        if($document){
          if($document->student_id==$etudiantId){
            unlink(storage_path().'/app/public/'.$document->rapport);
            $document->delete();
            return response([
                'message'=>'supprimé avec succès'
            ],200);
          }else{
            return response([
                'message'=>'Non autorisé à supprimé cet document.'
            ],401);
          }
        }else{
            return response([
                'message'=>'Document non existant'
            ],404);
        }
    }

    public function update(Request $request, $id){
       $attrs=$request->validate([
        'titre'=>'required|string',
        'description'=>'required|string'
       ]);

       $document=ClassDocument::where('id','=',$id)->first();;
       if($document){
            if($document->student_id==auth('sanctum')->user()->id){
                $document->update([
                    'titre'=>$attrs['titre'],
                    'description'=>$attrs['description']
                ]);

                return response([
                    'message'=>'document mis à jour avec succès'
                ],200);

            }else{
                return response([
                    'message'=>'Non autorisé à modifier le document'
                ],403);
            }
       
       }else{

        return response([
            'message'=>'document non existant'
        ],200);

       }
    }

    public function refuserAjout(Request $request, $id){
        $document=ClassDocument::where('id','=',$id)->first();
        $enseignantId=auth('sanctum')->user()->id;
        
        if($document){
            $studentId=$document->student_id;
            $student=Student::where('id','=',$studentId)->first();
            if($student){
                $matiereId=$document->matiere_id;
                $classes=EtudiantClasse::where('student_id','=',$student->id)->get('classe_id');
                foreach($classes as $classe){
                    $clss=Classe::where('id','=',$classe->classe_id)->where('type_id','=',1)->first();
                    if($clss){
                     $classeId=$clss->id;
                    }
                }
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
                    unlink(storage_path().'/app/public/'.$document->rapport);
                    $document->delete();
                    return response([
                        'message'=>'Document supprimé avec succès'
                    ],200);
                }else{
                    return response([
                        'message'=>'Non autorisé à supprimer ce document'
                    ],403); 
                }

            }else{
                    return response([
                        'message'=>'Etudiant non existant'
                    ],404); 
            }

        }else{
            return response([
                'message'=>'Document non existant'
            ],404); 
        }

       
    }


    public function confirmerAjout(Request $request, $id){
       $attrs=$request->validate([
        'matiere_id'=>'required|integer',
        'classe_id'=>'required|integer',
       ]);

       $enseignantId=auth('sanctum')->user()->id;
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
           $document=ClassDocument::find($id);
           if($document){
                if($document->flag=="cours"){
                        $fichier=Cours::create([
                            'matiere_id'=>$matiereId,
                            'titre'=>$document->titre,
                            'description'=>$document->description,
                            'file'=>$document->file
                        ]);
                        if($fichier){
                            $fichier->matiere()->associate($matiereId);
                            $document->delete();
                            return response([
                                'message'=>'Document ajouté avec succès',
                            ],200);
                        }else{
                            return response([
                                'message'=>'Oops.. il ya un problème',
                            ],500);
                        }   
                }elseif($document->flag=="Exercice"){
                        $fichier=Exercices::create([
                            'matiere_id'=>$matiereId,
                            'titre'=>$document->name,
                            'description'=>$document->description,
                            'file'=>$document->file
                        ]);
                        if($fichier){
                            $fichier->matiere()->associate($matiereId);
                            $document->delete();
                            return response([
                                'message'=>'Document ajouté avec succès',
                            ],200);
                        }else{
                            return response([
                                'message'=>'Oops.. il ya un problème',
                            ],500);
                        }   
                }elseif($document->flag=='Examen'){
                        $fichier=Examen::create([
                            'matiere_id'=>$matiereId,
                            'titre'=>$document->name,
                            'description'=>$document->description,
                            'file'=>$document->file
                        ]);
                        if($fichier){
                            $fichier->matiere()->associate($matiereId);
                            $document->delete();
                            return response([
                                'message'=>'Document ajouté avec succès',
                            ],200);
                        }else{
                            return response([
                                'message'=>'Oops.. il ya un problème',
                            ],500);
                        } 
                }else{
                    return response([
                        'message'=>'Ce flag est non supporté.'
                    ],500);  
                }
           
           }else{
            return response([
                'message'=>'Document non existant'
            ],404);
           }
        }       
    
}
}