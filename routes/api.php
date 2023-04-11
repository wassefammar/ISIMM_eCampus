<?php

use App\Http\Controllers\ExamenController;
use App\Http\Controllers\ExercicesController;
use App\Http\Controllers\RemarqueController;
use App\Models\Exercices;
use Illuminate\Http\Request;
use App\Models\ClassDocument;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ClasseController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\ClassDocumentController;

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

Route::get('students',[StudentController::class, 'fiit']);

//les matieres

Route::get('matieres',[MatiereController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_matiere',[MatiereController::class, 'store'])->middleware('auth:sanctum');
Route::post('ajouter_classe',[ClasseController::class, 'store'])->middleware('auth:sanctum');

Route::post('associer_classe_to_prof',[ClasseController::class, 'AssignClassToProf'])->middleware('auth:sanctum');


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


//les routes des examens

Route::get('examens',[ExamenController::class, 'index'])->middleware('auth:sanctum');
Route::get('download_examen', [ExamenController::class, 'download'])->middleware('auth:sanctum');
Route::apiResource('upload_examen',ExamenController::class)->only('store')->middleware('auth:sanctum');
Route::post('update_examen/{id}',[ExamenController::class, 'update'])->middleware('auth:sanctum');
Route::post('supprimer_examen/{id}',[ExamenController::class, 'destroy'])->middleware('auth:sanctum');


//les routes des remarques

Route::get('remarques',[RemarqueController::class, 'index'])->middleware('auth:sanctum');
Route::post('ajouter_remarque', [RemarqueController::class, 'store'])->middleware('auth:sanctum');
Route::post('update_remarque/{id}',[RemarqueController::class,'update'])->middleware('auth:sanctum');
Route::post('supprimer_remarques/{id}',[RemarqueController::class, 'destroy'])->middleware('auth:sanctum');




/****Groupe des apis liées aux etudiants */
 Route::group(['middleware'=>['auth:sanctum']], function(){
   Route::get('etudiant', [StudentController::class, 'user']);
   Route::post('update_etudiant', [StudentController::class, 'update']);

}); 


/****Groupe des apis liées aux enseignants */
 Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('enseignant', [EnseignantController::class, 'user']);
    Route::post('update_enseignant', [EnseignantController::class, 'update']);
 
 });  


 /****Groupe des apis liées aux admins */
  Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('admin', [AdminController::class, 'user']);
    Route::post('update_admin', [AdminController::class, 'update']);
 
 }); 
