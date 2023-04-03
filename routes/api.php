<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\EnseignantController;

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

Route::post('register_etudiant',[StudentController::class, 'register']);
Route::post('login_etudiant',[StudentController::class, 'login']);

Route::post('register_enseignant',[EnseignantController::class, 'register']);
Route::post('login_enseignant',[EnseignantController::class, 'login']);

Route::post('register_admin',[AdminController::class, 'register']);
Route::post('login_admin',[AdminController::class, 'login']);


/****Groupe des apis liées aux etudiants */
 Route::group(['middleware'=>['auth:sanctum']], function(){
   Route::get('etudiant', [StudentController::class, 'user']);
   Route::post('update_etudiant', [StudentController::class, 'update']);
   Route::post('logout_etudiant', [StudentController::class, 'logout']);

}); 


/****Groupe des apis liées aux enseignants */
 Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('enseignant', [EnseignantController::class, 'user']);
    Route::post('update_enseignant', [EnseignantController::class, 'update']);
    Route::post('logout_enseignant', [EnseignantController::class, 'logout']);
 
 });  


 /****Groupe des apis liées aux admins */
  Route::group(['middleware'=>['auth:sanctum']], function(){
    Route::get('admin', [AdminController::class, 'user']);
    Route::post('update_admin', [AdminController::class, 'update']);
    Route::post('logout_admin', [AdminController::class, 'logout']);
 
 }); 
