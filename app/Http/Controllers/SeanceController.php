<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Seance;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class SeanceController extends Controller
{
    // Récupérer toutes les séances avec les films associés (avec pagination)
    public function index(Request $request)
    {
        try {
            // Récupération de toutes les séances avec leurs films et salles associés
            $seances = Seance::with(['film', 'salle'])->paginate($request->input('per_page', 10));
            return ToolsControlleur::successResponse(
                $seances,
                'Liste des séances récupérée avec succès',
                ['count' => $seances->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération séances', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des séances : ' . $e->getMessage());
        }
    }

    // Création d'une séance
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'film_id' => 'required|exists:films,id',
            'salle_id' => 'required|exists:salles,id',
            'date_heure_debut' => 'required|date_format:Y-m-d H:i:s',
            'date_heure_fin' => 'required|date_format:Y-m-d H:i:s|after:date_heure_debut',
            'note' => 'nullable|string|max:255',
            'pu_place' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : ' . $validator->errors()->first(), $validator->errors());
        }

        try {
            $seance = Seance::create($validator->validated());
            return ToolsControlleur::successResponse($seance, 'Séance créée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur création séance', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de la séance : ' . $e->getMessage());
        }
    }

    // Récupérer une séance par ID (avec film et salle associés)
    public function show($id)
    {
        try {
            $seance = Seance::with(['film', 'salle'])->findOrFail($id);
            return ToolsControlleur::successResponse($seance, 'Séance récupérée avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur récupération séance', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération de la séance : ' . $e->getMessage());
        }
    }

    // Mettre à jour une séance
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'film_id' => 'required|exists:films,id',
            'salle_id' => 'required|exists:salles,id',
            'date_heure_debut' => 'required|date_format:Y-m-d H:i:s',
            'date_heure_fin' => 'required|date_format:Y-m-d H:i:s|after:date_heure_debut',
            'note' => 'nullable|string|max:255',
            'pu_place' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : ' . $validator->errors()->first(), $validator->errors());
        }

        try {
            $seance = Seance::with(['film', 'salle'])->findOrFail($id);
            $seance->update($validator->validated());
            return ToolsControlleur::successResponse(
                $seance,
                'Séance mise à jour avec succès'
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour séance', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de la séance : ' . $e->getMessage());
        }
    }

    // Supprimer une séance avec ses reservations
    public function destroy($id)
    {
        try {
            $seance = Seance::findOrFail($id);
            // Suppression des réservations associées
            $seance->reservations()->delete();
            // Suppression de la séance
            $seance->delete();
            return ToolsControlleur::successResponse(null, 'Séance et ses réservations supprimées avec succès');
        } catch (\Exception $e) {
            \Log::error('Erreur suppression séance', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de la séance : ' . $e->getMessage());
        }
    }

}
