<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Categorie;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class CategorieController extends Controller
{
    // Récupérer toutes les catégories
    public function index()
    {
        try {
            // Récupération de toutes les catégories en indiquant le nombre total de films pour chaque catégorie
            $categories = Categorie::withCount('films')->get();
            return ToolsControlleur::successResponse(
                $categories,
                'Liste des catégories récupérée avec succès',
                ['count' => $categories->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération catégories', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des catégories : ' . $e->getMessage());
        }
    }

    // Création d'une catégorie
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $categorie = Categorie::create($request->all());
            return ToolsControlleur::successResponse(
                $categorie,
                'Catégorie créée avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création catégorie', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de la catégorie : ' . $e->getMessage());
        }
    }

    // Récupérer une catégorie par son ID (avec la liste de ses films)
    public function show($id)
    {
        try {
            $categorie = Categorie::with(['films'])->findOrFail($id);
            return ToolsControlleur::successResponse(
                $categorie,
                'Catégorie récupérée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération catégorie', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération de la catégorie : ' . $e->getMessage());
        }
    }

    // Mettre à jour une catégorie
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->update($request->all());
            return ToolsControlleur::successResponse(
                $categorie,
                'Catégorie mise à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour catégorie', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de la catégorie : ' . $e->getMessage());
        }
    }

    // Supprimer une catégorie
    public function destroy($id)
    {
        try {
            $categorie = Categorie::findOrFail($id);
            $categorie->delete();
            return ToolsControlleur::successResponse(
                null,
                'Catégorie supprimée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression catégorie', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de la catégorie : ' . $e->getMessage());
        }
    }

}
