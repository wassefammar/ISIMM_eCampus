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
    public function indexForStudents(){
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

    public function indexForAdmin(){
        $resultats=Resultat::with('etudiant:id,nom,prenom,image')->get();
        if(count($resultats)>0){
            return response([
                'messaage'=>'voila les résultats',
                'resultats'=>$resultats
            ],200);
        }
        else{
            return response([
                'messaage'=>'Rien de résultats',
            ],404); 
        }
    }




    public function store(Request $request)
    {
        $attrs = $request->validate([
            'matiere_id' => 'required|integer',
            'resultats' => 'required|array',
        ]);
    
        $matiereId = $attrs['matiere_id'];
        $resultats = $attrs['resultats'];
    
        foreach ($resultats as $resultat) {
            // Convert the array to a JSON string
            $string = json_encode($resultat);
    
            // Convert the JSON string back to an associative array
            $resultat = json_decode($string, true);
    
            $etudiantId = $resultat['etudiant_id'];
    
            $exist = Resultat::where('matiere_id', '=', $matiereId)
                            ->where('student_id', '=', $etudiantId)
                            ->first();
    
            if ($exist) {
                $exist->update([
                    'note_TD' => $resultat['note_TD'],
                    'note_TP' => $resultat['note_TP'],
                    'note_DS' => $resultat['note_DS'],
                    'note_Examen' => $resultat['note_Examen'],
                    'moyenne' => $resultat['moyenne'],
                    'credit' => $resultat['credit']
                ]);
            } else {
                $etudiant = Student::find($etudiantId);
    
                if ($etudiant) {
                    $classes = EtudiantClasse::where('student_id', '=', $etudiantId)->get('classe_id');
                    $matiere = MatiereClasse::whereIn('classe_id', $classes)->where('matiere_id', '=', $matiereId)->first();
    
                    if ($matiere) {
                        Resultat::create([
                            'matiere_id' => $matiereId,
                            'student_id' => $etudiantId,
                            'note_TD' => $resultat['note_TD'],
                            'note_TP' => $resultat['note_TP'],
                            'note_DS' => $resultat['note_DS'],
                            'note_Examen' => $resultat['note_Examen'],
                            'moyenne' => $resultat['moyenne'],
                            'credit' => $resultat['credit']
                        ]);
                    } else {
                        continue;
                    }
                }
            }
        }
    
        return response([
            'message' => 'Tous les résultats ont été ajoutés.'
        ], 200);
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
