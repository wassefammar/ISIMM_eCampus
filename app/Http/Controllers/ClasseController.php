<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use Illuminate\Http\Request;

class ClasseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $classes=Classe::all();
        if($classes){
            return response()->json([
                'classes'=>$classes
            ],200);
        }
        else{
            return response()->json([
                'message'=>'pas de classes pour le moment'
            ],200);
        }
        
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $attrs= $request->validate([
            'nom'=>'required|string'
        ]);
        $classe=Classe::create([
            'nom'=>$attrs['nom']
        ]);
        return response([
            'message'=>'Classe crée avec succès',
            'post'=> $classe
        ],200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $classe=Classe::find($id);
        if(!$classe){
            return response([
                'message'=>'Classe inexistante',
            ],403);
        }
        else{
            $attrs= $request->validate([
                'nom'=>'required|string'
            ]);
            $classe->update([
                'nom'=>$attrs['nom']
            ]);
            
            return response([
                'message'=>'Classe mise à jour',
                'classe'=> $classe
            ],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Classe  $classe
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //

        $classe=Classe::find($id);
        if($classe){
            $classe->delete();
            return response()->json([
                'message'=>'Classe supprimée avec succès'

            ],200);
        }
        else{
            return response()->json([
                'message'=>'Classe inexistante'

            ],200); 
        }
    }
}
