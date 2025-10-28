<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Salle;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class SalleController extends Controller
{
    // récupération de toutes les salles
    public function index()
    {
        try {
            $salles = Salle::all();
            return ToolsControlleur::successResponse(
                $salles,
                'Liste des salles récupérée avec succès',
                ['count' => $salles->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération salles', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des salles : ' . $e->getMessage());
        }
    }

    //création d'une salle
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'capacite' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $salle = Salle::create($request->all());
            return ToolsControlleur::successResponse(
                $salle,
                'Salle créée avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création salle', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de la salle : ' . $e->getMessage());
        }
    }

    //récupération d'une salle (et ses infos) par son id
    public function show($id)
    {
        try {
            $salle = Salle::with('seances')->findOrFail($id);
            return ToolsControlleur::successResponse(
                $salle,
                'Salle récupérée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération salle', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération de la salle : ' . $e->getMessage());
        }
    }

    // mise à jour d'une salle par son id
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'adresse' => 'sometimes|required|string|max:255',
            'capacite' => 'sometimes|required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $salle = Salle::findOrFail($id);
            $salle->update($request->all());
            return ToolsControlleur::successResponse(
                $salle,
                'Salle mise à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour salle', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de la salle : ' . $e->getMessage());
        }
    }

    // suppression d'une salle par son id
    public function destroy($id)
    {
        try {
            $salle = Salle::findOrFail($id);
            // supprimer les séances associées à la salle et les réservations associées à chaque séance
            foreach ($salle->seances as $seance) {
                foreach ($seance->reservations as $reservation) {
                    $reservation->delete();
                }
                $seance->delete();
            }
            $salle->delete();
            return ToolsControlleur::successResponse(
                null,
                'Salle supprimée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression salle', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de la salle : ' . $e->getMessage());
        }
    }
}
