<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password'])
        ]);

        $token = $user->createToken($request->name)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }

    public function login(Request $request)
{
    // Validation des données d'entrée
    $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    // Recherche de l'utilisateur par email
    $user = User::where('email', $request->email)->first();

    // Vérification des informations d'identification
    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json([
            'errors' => ['credentials' => ['Invalid credentials.']]
        ], 401);
    }

    // Création du token
    $token = $user->createToken('Personal Access Token')->plainTextToken;

    // Réponse avec les détails de l'utilisateur et le token
    return response()->json([
        'user' => $user,
        'token' => $token
    ]);
}


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ['message' => 'Logout successful!'];
    }


}
