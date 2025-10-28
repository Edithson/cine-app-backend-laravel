<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Avis;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class AvisController extends Controller
{
    // Récupérer tous les avis avec les utilisateurs associés
    public function index()
    {
        try {
            // Récupération de tous les avis avec leurs utilisateurs associés
            $avis = Avis::with('user')->get();
            return ToolsControlleur::successResponse(
                $avis,
                'Liste des avis récupérée avec succès',
                ['count' => $avis->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération avis', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des avis : ' . $e->getMessage());
        }
    }

    // Récupérer les avis d'un utilisateur spécifique
    public function getAvisByUser($userId)
    {
        try {
            $avis = Avis::where('user_id', $userId)->with('user')->get();
            return ToolsControlleur::successResponse(
                $avis,
                'Liste des avis récupérée avec succès',
                ['count' => $avis->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération avis par utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des avis : ' . $e->getMessage());
        }
    }

    // Récupérer les avis d'un films spécifique
    public function getAvisByFilm($filmId)
    {
        try {
            $avis = Avis::where('film_id', $filmId)->with('user')->get();
            return ToolsControlleur::successResponse(
                $avis,
                'Liste des avis récupérée avec succès',
                ['count' => $avis->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération avis par film', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des avis : ' . $e->getMessage());
        }
    }

    // Ajouter un nouvel avis
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'film_id' => 'required|exists:films,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation échouée : '.$validator->errors(), $validator->errors(), 422);
        }

        try {
            $avis = Avis::create($request->all());
            return ToolsControlleur::successResponse(
                $avis,
                'Avis créé avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création avis', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de l\'avis : ' . $e->getMessage());
        }
    }

    // mettre à jour un avis existant
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'note' => 'sometimes|required|integer|min:1|max:5',
            'commentaire' => 'sometimes|nullable|string',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation échouée : '.$validator->errors(), $validator->errors(), 422);
        }

        try {
            $avis = Avis::findOrFail($id);
            $avis->update($request->all());
            return ToolsControlleur::successResponse(
                $avis,
                'Avis mis à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour avis', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de l\'avis : ' . $e->getMessage());
        }
    }

    // Supprimer un avis
    public function destroy($id)
    {
        try {
            $avis = Avis::findOrFail($id);
            $avis->delete();
            return ToolsControlleur::successResponse(
                null,
                'Avis supprimé avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression avis', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de l\'avis : ' . $e->getMessage());
        }
    }

}
