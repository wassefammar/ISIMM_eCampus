<?php

use App\Models\StudentChatAdmin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\DeslikeController;
use App\Http\Controllers\EpreuveController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PFEBookController;
use App\Http\Controllers\SocieteController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\RemarqueController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\ExercicesController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\RapportPFEController;
use App\Http\Controllers\EmploiTempsController;
use App\Http\Controllers\ListPresenceController;
use App\Http\Controllers\ClassDocumentController;
use App\Http\Controllers\SessionMatiereController;
use App\Http\Controllers\ListParticipantsController;
use App\Http\Controllers\StudentChatAdminController;
use App\Http\Controllers\EnseignantChatAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

Route::post('register_enseignant',[EnseignantController::class, 'register']);
Route::post('register_etudiant',[StudentController::class, 'register']);
Route::post('register_admin',[AdminController::class, 'register']);
Route::post('login',[LoginController::class, 'login']);
Route::post('logout',[LoginController::class, 'logout'])->middleware('auth:sanctum');

//Route::get('students',[StudentController::class, 'fiit']);

//les matieres

Route::get('matieres',[MatiereController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_matiere',[MatiereController::class, 'store'])->middleware('auth:sanctum');
Route::post('ajouter_classe',[ClasseController::class, 'store'])->middleware('auth:sanctum');

//liste Matiere()

Route::get('list_matieres', [MatiereController::class, 'ListMatieres'])->middleware('auth:sanctum');
//les routes des cours

Route::get('cours',[CoursController::class, 'index'])->middleware('auth:sanctum');
Route::get('download_cours', [CoursController::class, 'download'])->middleware('auth:sanctum');
Route::apiResource('upload_cours',CoursController::class)->only('store')->middleware('auth:sanctum');
Route::post('update_cours/{id}',[CoursController::class, 'update'])->middleware('auth:sanctum');
Route::post('supprimer_cours/{id}',[CoursController::class, 'destroy'])->middleware('auth:sanctum');


//les routes des exercices

Route::get('exercices',[ExercicesController::class, 'index'])->middleware('auth:sanctum');
Route::get('download_exercice', [ExercicesController::class, 'download'])->middleware('auth:sanctum');
Route::apiResource('upload_exercice',ExercicesController::class)->only('store')->middleware('auth:sanctum');
Route::post('update_exercice/{id}',[ExercicesController::class, 'update'])->middleware('auth:sanctum');
Route::post('supprimer_exercice/{id}',[ExercicesController::class, 'destroy'])->middleware('auth:sanctum');



//les routes des séances

Route::get('seances',[SessionMatiereController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_seance',[SessionMatiereController::class, 'store'])->middleware('auth:sanctum');
Route::post('update_seance/{id}', [SessionMatiereController::class, 'update'])->middleware('auth:sanctum');




//les routes des annonces


Route::get('annonces',[AnnonceController::class, 'index'])->middleware('auth:sanctum');
Route::post('like/{id}',[LikeController::class, 'likeOrUnlike'])->middleware('auth:sanctum');
Route::post('deslike/{id}',[DeslikeController::class, 'deslikeOrUndeslike'])->middleware('auth:sanctum');
Route::post('update_annonce/{id}',[AnnonceController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_annonce/{id}',[AnnonceController::class, 'destroy'])->middleware('auth:sanctum');

//Messages
Route::post('messages',[MessageController::class, 'store'])->middleware('auth:sanctum');
Route::get('chat_rooms',[ChatRoomController::class, 'index'])->middleware('auth:sanctum');
Route::get('messages/{id}', [MessageController::class, 'index'])->middleware('auth:sanctum');

//list participants
Route::get('list_participants',[ListParticipantsController::class, 'index'])->middleware('auth:sanctum');
Route::post('bloquer_participant',[ListParticipantsController::class, 'blockParticipant'])->middleware('auth:sanctum');
Route::post('ajouter_participant',[ListParticipantsController::class, 'ajouterParticipant'])->middleware('auth:sanctum');


//contacter administration etudiant chatMessages
Route::get('chat_messages_etudiant/{id}',[StudentChatAdminController::class, 'chatMessages'])->middleware('auth:sanctum');
Route::get('list_chats_etudiants',[StudentChatAdminController::class, 'indexForAdmin'])->middleware('auth:sanctum');
Route::post('repondre_etudiant',[StudentChatAdminController::class, 'repondreEtudiant'])->middleware('auth:sanctum');
Route::post('contacter_admin',[StudentChatAdminController::class, 'contacterAdmin'])->middleware('auth:sanctum');

//contacter administration etudiant
Route::get('chat_messages_enseignant/{id}',[EnseignantChatAdminController::class, 'chatMessages'])->middleware('auth:sanctum');
Route::get('list_chats_enseignants',[EnseignantChatAdminController::class, 'indexForAdmin'])->middleware('auth:sanctum');
Route::post('repondre_enseignant',[EnseignantChatAdminController::class, 'repondreEtudiant'])->middleware('auth:sanctum');
Route::post('enseignant/contacter_admin',[EnseignantChatAdminController::class, 'contacterAdmin'])->middleware('auth:sanctum');


//les epreuves
Route::get('epreuves',[EpreuveController::class, 'indexForAdmin'])->middleware('auth:sanctum');
Route::get('list_epreuves',[EpreuveController::class, 'indexForStudents'])->middleware('auth:sanctum');
Route::post('ajouter_epreuve',[EpreuveController::class, 'store'])->middleware('auth:sanctum');
Route::patch('update_epreuve/{id}',[EpreuveController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_epreuve/{id}',[EpreuveController::class, 'destroy'])->middleware('auth:sanctum');


//les résultats
Route::get('mes_resultats',[ResultatController::class, 'index'])->middleware('auth:sanctum');
Route::post('confirmer_resultat',[ResultatController::class, 'store'])->middleware('auth:sanctum');
Route::delete('supprimer_resultat/{id}',[ResultatController::class, 'destroy'])->middleware('auth:sanctum');



//les routes des examens

Route::get('examens',[ExamenController::class, 'index'])->middleware('auth:sanctum');
Route::get('download_examen', [ExamenController::class, 'download'])->middleware('auth:sanctum');
Route::apiResource('upload_examen',ExamenController::class)->only('store')->middleware('auth:sanctum');
Route::post('update_examen/{id}',[ExamenController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_examen/{id}',[ExamenController::class, 'destroy'])->middleware('auth:sanctum');


//les routes pour les document de liste d'attente

Route::get('document_en_attente', [ClassDocumentController::class,'indexEnseignant'])->middleware('auth:sanctum');
Route::get('mes_documents', [ClassDocumentController::class,'indexEtudiant'])->middleware('auth:sanctum');
Route::post('ajouter_document', [ClassDocumentController::class, 'store'])->middleware('auth:sanctum');
Route::post('confirmer_ajout/{id}', [ClassDocumentController::class, 'confirmerAjout'])->middleware('auth:sanctum');
Route::post('refuser_ajout/{id}', [ClassDocumentController::class, 'refuserAjout'])->middleware('auth:sanctum');
Route::post('download_document',[ClassDocumentController::class, 'download'])->middleware('auth:sanctum');
Route::post('update_document/{id}', [ClassDocumentController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_document/{id}', [ClassDocumentController::class, 'destroy'])->middleware('auth:sanctum');


//les routes du rapport PFE
Route::get('rapports',[RapportPFEController::class, 'index'])->middleware('auth:sanctum');
Route::post('telecharger_rapport',[RapportPFEController::class, 'download'])->middleware('auth:sanctum');
Route::post('ajouter_rapport',[RapportPFEController::class, 'store'])->middleware('auth:sanctum');
Route::patch('update_rapport/{id}',[RapportPFEController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_rapport/{id}', [RapportPFEController::class, 'destroy'])->middleware('auth:sanctum');


//liste des etudiants
Route::get('etudiants',[StudentController::class, 'listEtudiants'])->middleware('auth:sanctum');
Route::post('modifier_etudiant/{id}',[StudentController::class, 'modifierEtudiant'])->middleware('auth:sanctum');
Route::delete('supprimer_etudiant/{id}',[StudentController::class, 'supprimerEtudiant'])->middleware('auth:sanctum');


//list des enseignants
Route::get('enseignants',[EnseignantController::class, 'listEnseignants'])->middleware('auth:sanctum');
Route::post('modifier_enseignant/{id}',[EnseignantController::class, 'modifierEnseignant'])->middleware('auth:sanctum');
Route::delete('supprimer_enseignant/{id}',[EnseignantController::class, 'supprimerEnseignant'])->middleware('auth:sanctum');

//les routes des societes
Route::get('societes',[SocieteController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_societe', [SocieteController::class, 'store'])->middleware('auth:sanctum');
Route::patch('update_societe/{id}',[SocieteController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_societe/{id}',[SocieteController::class, 'destroy'])->middleware('auth:sanctum');

//les routes des PFEBook
Route::get('pfeBooks',[PFEBookController::class, 'index'])->middleware('auth:sanctum');
Route::post('telecharger_pfeBook',[PFEBookController::class, 'download'])->middleware('auth:sanctum');
Route::post('ajouter_pfeBook',[PFEBookController::class, 'store'])->middleware('auth:sanctum');
Route::patch('update_pfeBook/{id}',[PFEBookController::class, 'update'])->middleware('auth:sanctum');
Route::delete('supprimer_pfeBook/{id}', [PFEBookController::class, 'destroy'])->middleware('auth:sanctum');


//les routes des remarques

Route::get('remarques',[RemarqueController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_remarque', [RemarqueController::class, 'store'])->middleware('auth:sanctum');
Route::post('update_remarque/{id}',[RemarqueController::class,'update'])->middleware('auth:sanctum');
Route::delete('supprimer_remarques/{id}',[RemarqueController::class, 'destroy'])->middleware('auth:sanctum');

//mes seances
Route::get('mes_seances',[EmploiTempsController::class, 'indexForEnseignants'])->middleware('auth:sanctum');
Route::get('emploi_temps',[EmploiTempsController::class, 'indexForStudents'])->middleware('auth:sanctum');
//admin
Route::get('emploi_classe',[EmploiTempsController::class, 'indexForAdmins'])->middleware('auth:sanctum');

//annonces
Route::delete('supprimer_annonce/{id}',[AnnonceController::class, 'destroy'])->middleware('auth:sanctum');


/****Groupe des apis liées aux etudiants */
 Route::group(['middleware'=>['auth:sanctum', 'abilities:etudiant']], function(){
   Route::get('etudiant', [StudentController::class, 'user']);
   Route::post('update_etudiant', [StudentController::class, 'update']);

   //les routes pour les document de liste d'attente

   
   Route::get('mes_documents', [ClassDocumentController::class,'indexEtudiant'])->middleware('auth:sanctum');
   Route::post('ajouter_document', [ClassDocumentController::class, 'store'])->middleware('auth:sanctum');
   Route::post('download_document',[ClassDocumentController::class, 'download'])->middleware('auth:sanctum');
   Route::post('update_document/{id}', [ClassDocumentController::class, 'update'])->middleware('auth:sanctum');
   Route::delete('supprimer_document/{id}', [ClassDocumentController::class, 'destroy'])->middleware('auth:sanctum');

   //annonces
   Route::post('etudiant/ajouter_annonce',[AnnonceController::class, 'storeForEnseignants'])->middleware('auth:sanctum');



}); 


/****Groupe des apis liées aux enseignants */
 Route::group(['middleware'=>['auth:sanctum','abilities:enseignant']], function(){
    Route::get('enseignant', [EnseignantController::class, 'user']);
    Route::post('update_enseignant', [EnseignantController::class, 'update']);

    //les routes pour les document de liste d'attente
    
    Route::get('document_en_attente', [ClassDocumentController::class,'indexEnseignant']);
    Route::post('confirmer_ajout/{id}', [ClassDocumentController::class, 'confirmerAjout']);
    Route::post('refuser_ajout/{id}', [ClassDocumentController::class, 'refuserAjout']);
    Route::post('download_document',[ClassDocumentController::class, 'download']);


    //les routes des remarques

    Route::get('remarques',[RemarqueController::class, 'index']); 
    Route::post('ajouter_remarque', [RemarqueController::class, 'store']);
    Route::post('update_remarque/{id}',[RemarqueController::class,'update']);
    Route::delete('supprimer_remarques/{id}',[RemarqueController::class, 'destroy']);


    //les routes pour la présence
    Route::get('liste_presence',[ListPresenceController::class, 'index']);
    Route::post('confirmer_presence',[ListPresenceController::class, 'store']);
    Route::post('update_presence', [ListPresenceController::class, 'update']);

    //emploi
    Route::get('emploi',[EmploiTempsController::class, 'indexForEnseignants']);

   //annonce
    Route::post('enseignant/ajouter_annonce',[AnnonceController::class, 'storeForEnseignants'])->middleware('auth:sanctum');


 
 });  


 /****Groupe des apis liées aux admins */
  Route::group(['middleware'=>['auth:sanctum', 'abilities:admin']], function(){
    Route::get('admin', [AdminController::class, 'user']);
    Route::post('update_admin', [AdminController::class, 'update']);
    Route::post('associer_enseignant_classe', [ClasseController::class, 'AssignClassToProf']);
    Route::post('associer_matiere_classe',[MatiereController::class,'AssignMatiereToClass']);
    Route::post('desassocier_enseignant_classe',[ClasseController::class,'desassocierEnseignantClasse']);
    Route::post('associer_etudiant_classe',[ClasseController::class,'AssignStudentToClass']);
    Route::post('desassocier_etudiant_classe',[ClasseController::class,'desassocierEtudiantClasse']);
    //emploi
    Route::post('ajouter_emploi',[EmploiTempsController::class, 'store']);
    Route::delete('supprimer_emploi/{id}', [EmploiTempsController::class, 'destroy']);

    //admin
    Route::post('admin/ajouter_annonce',[AnnonceController::class, 'storeForAdmins'])->middleware('auth:sanctum');

 
 }); 
