<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Authentifier un utilisateur
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            \Log::error('Erreur validation login', ['error' => $validator->errors()->first()]);
            return ToolsControlleur::errorResponse('Validation échouée : '.$validator->errors()->first(), $validator->errors());
        }

        try {
            if (!auth()->attempt($request->only('email', 'password'))) {
                return ToolsControlleur::errorResponse('Identifiants invalides');
            }

            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return ToolsControlleur::successResponse(
                ['user' => $user, 'token' => $token],
                'Utilisateur authentifié avec succès',
                200
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur lors de l\'authentification', ['error' => $th->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de l\'authentification : ' . $th->getMessage());
        }
    }

    // Déconnexion de l'utilisateur
    public function logout()
    {
        try {
            auth()->user()->tokens()->delete();
            auth()->logout();
            return ToolsControlleur::successResponse(
                null,
                'Utilisateur déconnecté avec succès',
                200
            );
        } catch (\Throwable $th) {
            \Log::error('Erreur lors de la déconnexion', ['error' => $th->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la déconnexion : ' . $th->getMessage());
        }
    }

}
