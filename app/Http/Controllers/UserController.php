<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::get()->toArray();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
            $user = User::create($request->all());
        
            return response()->json([
                'status' => true,
                'message' => "User Created successfully!",
                'user' => $user
            ], 201);
    }
    
    /**
     * Display the specified resource.
     */
    public function show(User $users)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $users)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $users)
    {
        //
    }

    // Asignar rol a un usuario
    public function assignRole(Request $request, $userId)
    {
        $user = User::find($userId);
        $roleId = $request->input('role_id');
        $role = Role::find($roleId);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        $user->role = $roleId;
        $user->save();

        return response()->json(['message' => 'Role assigned successfully', 'user' => $user]);
    }

}
