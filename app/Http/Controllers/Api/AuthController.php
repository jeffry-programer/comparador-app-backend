<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthController extends Controller
{    
    public function allUsers(){
        $users = User::all();
        return response()->json($users);
    }

    public function register(Request $request){
        //Validacion de los datos
        $request->validate([
            'correo' => 'required|email|unique:usuario',
            'Clave' => 'required'
        ]);

        //registrar al usuario
        $user = new User();
        $user->Perfil_idPerfil = 1;
        $user->correo = $request->correo;
        $user->Clave = Hash::make($request->Clave);
        $user->verificar = 0;
        $user->modoAcceso = "directo";
        $user->fechaRegistro =  Carbon::now();
        $user->emailEncriptado = Hash::make($request->correo);
        $user->save();
 
        //respuesta
        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request){
        //Validacion de los datos
        $credentials = $request->validate([
            'correo' => ['required','email'],
            'Clave' => ['required']
        ]);

        $user = User::where("correo","=",$request->correo)->first();

        if(isset($user->idUsuario)){
            if(Hash::check($request->Clave, $user->Clave)){
                //Creamos el token
                $token = Str::random(60);
 
                //Si todo esta bien
                return response()->json([
                    "status" => 1,
                    "msg" => "Usuario logueado exitosamente",
                    "access_token" => $token,
                    "userId" => $user->idUsuario
                ], 200);
            }else{
                return response()->json([
                    "status" => 0,
                    "msg" => "La contraseña es incorrecta"
                ], 401);
            }
        }else{
            return response()->json([
                "status" => 0,
                "msg" => "El usuario no esta registrado"
            ], 401);
        }
    }/*

    public function userProfile(Request $request){
        return response()->json([
            "message" => "userProfile Ok",
            "userData" =>  auth()->user()
        ], Response::HTTP_OK);
    }

    public function logout(){
        $cookie = Cookie::forget('cookie_token');
        return response(["message"=>"Cierre de sesión ok"], Response::HTTP_OK)->withCookie($cookie);
    }*/
}
