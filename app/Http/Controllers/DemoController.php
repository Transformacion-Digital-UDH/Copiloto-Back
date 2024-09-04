<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DemoController extends Controller
{
    public function login(Request $request)
    {
        $input = $request->all();

        if (Auth::attempt(['email' => $input['email'], 'password' => $input['password']])) {
            $user = Auth::user();
            $token = $user->createToken('appuser');

            return response()->json([
                'token' => $token->plainTextToken
            ]);
        } else {
            // Respuesta de error si las credenciales no son correctas
            return response()->json([
                'error' => 'Las credenciales no son correctas.',
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Se desconectó con éxito'], 200);
    }

    //
}
