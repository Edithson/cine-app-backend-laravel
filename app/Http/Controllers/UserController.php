<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Récupérer tous les utilisateurs avec pagination
    public function index(Request $request)
    {
        try {
            $users = User::paginate($request->input('per_page', 15));
            return ToolsControlleur::successResponse(
                $users,
                'Liste des utilisateurs récupérée avec succès',
                ['count' => $users->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération utilisateurs', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des utilisateurs : ' . $e->getMessage());
        }
    }

    // Récupérer un utilisateur par son ID (avec ses reservations et ses avis)
    public function show($id)
    {
        try {
            $user = User::with(['reservations', 'avis'])->findOrFail($id);
            return ToolsControlleur::successResponse(
                $user,
                'Utilisateur récupéré avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Créer un nouvel utilisateur
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'type_id' => 'required|integer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation échouée : '.$validator->errors()->first(), $validator->errors());
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'type_id' => $request->type_id,
                'password' => bcrypt($request->password),
            ]);
            return ToolsControlleur::successResponse(
                $user,
                'Utilisateur créé avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Mettre à jour un utilisateur existant
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$id,
            'type_id' => 'sometimes|required|integer',
            'password' => 'sometimes|required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation échouée : '.$validator->errors()->first(), $validator->errors());
        }

        try {
            $user = User::findOrFail($id);
            $user->update($request->only(['name', 'email', 'type_id', 'password']));
            return ToolsControlleur::successResponse(
                $user,
                'Utilisateur mis à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Supprimer un utilisateur
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            // Supprimer les reservations de l'utilisateur
            $user->reservations()->delete();
            // Supprimer les avis de l'utilisateur
            $user->avis()->delete();
            // Supprimer l'utilisateur
            $user->delete();
            return ToolsControlleur::successResponse(
                null,
                'Utilisateur supprimé avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        }
    }

    // Restaurer un utilisateur supprimé
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            // restaurer les reservations de l'utilisateur
            foreach ($user->reservations()->withTrashed()->get() as $reservation) {
                $reservation->restore();
            }
            // restaurer les avis de l'utilisateur
            foreach ($user->avis()->withTrashed()->get() as $avis) {
                $avis->restore();
            }
            // restaurer l'utilisateur
            $user->restore();
            return ToolsControlleur::successResponse(
                $user,
                'Utilisateur restauré avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur restauration utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la restauration de l\'utilisateur : ' . $e->getMessage());
        }
    }

}
