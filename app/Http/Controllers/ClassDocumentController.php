<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\ClassDocument;
use Illuminate\Support\Facades\Storage;

class ClassDocumentController extends Controller
{


    public function store(Request $request)
    {
        //
        $attrs=$request->validate([
            'name'=>'required|string',
            'description'=>'nullable|string',
            'file' => 'required|mimes:pdf|max:2048'     
        ]);
        $fileName= Str::random(20).".".$attrs['file']->getClientOriginalExtension();
        $fichier=ClassDocument ::create([
            'titre'=>$attrs['name'],
            'description'=>$attrs['description'],
            'file'=>$fileName
        ]);
        if($fichier){
            Storage::disk('public')->put($fileName, file_get_contents($attrs['file']));
            return response([
                'message'=>'Document ajouté avec succès',
            ],200);
        }else{
            return response([
                'message'=>'Oops.. il ya un problème',
            ],500);
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
}
