<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                'error' => ['Las credenciales no son correctas.'],
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Usuario autenticado correctamente.',
            'token' => $token,
            'data' =>  new UserResource($user)
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Se desconectó con éxito'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => true,
            'user' => [
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
            ],
        ], 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:60|min:15',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|max:15',
            'role' => 'required|string|in:admin,estudiante,paisi,coordinador,facultad'
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es un correo electronico',
            'email.unique' => 'El correo electrónico ya se encuentra registrado',
            'email.max' => 'El correo electrónico debe tener como maximo :max caracteres',
            'password.required' => 'La contraseña es requerido',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.max' => 'La contraseña debe tener como maximo :max caracteres',
            'role.in' => 'El rol debe ser admin, estudiante, paisi, coordinador o facultad',
            'role.required' => 'El rol es requerido',
        ];

        $validator = Validator::make($request->input(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Usuario registrado correctamente',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $user->createToken('api_token')->plainTextToken
        ], 200);
    }
}
