<?php

use Illuminate\Http\Request;
use App\Models\ClassDocument;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
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
Route::get('download', [ClassDocumentController::class, 'download'])->middleware('auth:sanctum');
Route::apiResource('upload_file',ClassDocumentController::class)->only('store')->middleware('auth:sanctum');







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
