<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:60',
            'password' => 'required|string|min:6|max:15',
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es un correo electronico',
            'email.max' => 'El correo electrónico debe tener como maximo :max caracteres',
            'password.required' => 'La contraseña es requerido',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.max' => 'La contraseña debe tener como maximo :max caracteres',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 400);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'error' => 'Las credenciales no son correctas.',
            ], 401);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Usuario autenticado correctamente.',
                'token' => $token,
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Se desconectó con éxito'], 200);
    }

}
