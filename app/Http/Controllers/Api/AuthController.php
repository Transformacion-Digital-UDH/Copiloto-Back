<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Student;

class AuthController extends Controller
{
    public function loginGoogle(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:60',
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es un correo electronico',
            'email.max' => 'El correo electrónico debe tener como maximo :max caracteres'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 400);
        }

        if (!User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'error' => ['Correo y/o contraseña incorrectos.'],
            ], 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Usuario autenticado correctamente.',
            'token' => $token,
            'data' =>  new UserResource($user)
        ], 200);
    }

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
                'error' => ['Correo y/o contraseña incorrectos.'],
            ], 401);
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Usuario autenticado correctamente.',
                'token' => $token,
                'data' =>  new UserResource($user)
            ], 200);
        }
    }

    public function logout(Request $request)
    {   
        $user = $request->user();
        
        if(!$user){
            return response()->json([
                'status' => false,
                'message' => 'No se encontro el usuario'
            ], 404);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Se desconectó con éxito'
        ], 200);
    }

    public function me(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'No se encontro el usuario'
            ], 404);
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'estudiante_id' => $request->user()->student->_id,
                'nombre' => $request->user()->name,
                'correo' => $request->user()->email,
                'rol' => $request->user()->role->name,
            ],
        ], 200);
    }

    public function register(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|max:15',
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es un correo electronico',
            'email.unique' => 'El correo electrónico ya se encuentra registrado',
            'email.max' => 'El correo electrónico debe tener como maximo :max caracteres',
            'password.required' => 'La contraseña es requerido',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.max' => 'La contraseña debe tener como maximo :max caracteres',
        ];

        $validator = Validator::make($request->input(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'estudiante')->value('_id'),
        ]);

        // Crea el registro en la colección students
        Student::create([
            'stu_name' => '',
            'stu_lastname_m' => '',
            'stu_latsname_f' => '',
            'stu_dni' => '',
            'stu_code' => '',
            'user_id' => $user->_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Usuario registrado correctamente',
            'token' => $user->createToken('api_token')->plainTextToken,
            'data' => new UserResource($user)
        ], 200);
    }

    public function registerGoogle(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|max:15',
        ];

        $messages = [
            'email.required' => 'El correo electrónico es requerido',
            'email.email' => 'El correo electrónico no es un correo electronico',
            'email.unique' => 'El correo electrónico ya se encuentra registrado',
            'email.max' => 'El correo electrónico debe tener como maximo :max caracteres',
            'password.required' => 'La contraseña es requerido',
            'password.min' => 'La contraseña debe tener al menos :min caracteres',
            'password.max' => 'La contraseña debe tener como maximo :max caracteres',
        ];

        $validator = Validator::make($request->input(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->errors()->all()
            ], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => Role::where('name', 'estudiante')->value('_id'),
        ]);

        // Crea el registro en la colección students 
        Student::create([
            'stu_name' => '',
            'stu_lastname_m' => '',
            'stu_latsname_f' => '',
            'stu_dni' => '',
            'stu_code' => '',
            'user_id' => $user->_id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Usuario registrado correctamente',
            'token' => $user->createToken('api_token')->plainTextToken,
            'data' => new UserResource($user)
        ], 200);
    }
}
