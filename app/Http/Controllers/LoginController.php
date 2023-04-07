<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //

    public function login(Request $request){
        $attrs= $request->validate([
            'email'=>'required|email',
            'password' => 'required|min:6'
        ]);
        if(Auth::guard('students')->attempt($attrs)){
            return response([
                'user'=>Auth::guard('students')->user(),
                'token'=>Auth::guard('students')->user()->createToken('secret')->plainTextToken
            ],200);
        }
        elseif(Auth::guard('enseignant')->attempt($attrs)){
            return response([
                'user'=>Auth::guard('enseignant')->user(),
                'token'=>Auth::guard('enseignant')->user()->createToken('secret')->plainTextToken
            ],200);
        }
        elseif(Auth::guard('admins')->attempt($attrs)){
            return response([
                'user'=>Auth::guard('admins')->user(),
                'token'=>Auth::guard('admins')->user()->createToken('secret')->plainTextToken
            ],200);
        }
        else{
            return response([
                'message'=>'Vérifier vos informations',
               ], 403);
        }

    }

    public function logout(Request $request){
        auth('sanctum')->user()->tokens()->delete();
        return response()->json([
            'message'=> 'Déconnecté'
        ],200);
    }
}
