<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    // Récupérer toutes les réservations avec les séances associées
    public function index()
    {
        try {
            // Récupération de toutes les réservations avec leurs séances associées
            $reservations = Reservation::with('seance')->get();
            return ToolsControlleur::successResponse(
                $reservations,
                'Liste des réservations récupérée avec succès',
                ['count' => $reservations->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération réservations', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des réservations : ' . $e->getMessage());
        }
    }

    // reccupérer les réservations d'un utilisateur spécifique dont la date est future
    public function getReservationsByUser($userId)
    {
        try {
            $reservations = Reservation::where('user_id', $userId)
                ->where('created_at', '>', now())
                ->with('seance')
                ->get();
            return ToolsControlleur::successResponse(
                $reservations,
                'Liste des réservations récupérée avec succès',
                ['count' => $reservations->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération réservations par utilisateur', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des réservations : ' . $e->getMessage());
        }
    }

    // récupérer les réservations d'une séance spécifique
    public function getReservationsBySeance($seanceId)
    {
        try {
            $reservations = Reservation::where('seance_id', $seanceId)->with('user')->get();
            return ToolsControlleur::successResponse(
                $reservations,
                'Liste des réservations récupérée avec succès',
                ['count' => $reservations->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération réservations par séance', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des réservations : ' . $e->getMessage());
        }
    }

    // Création d'une réservation
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'seance_id' => 'required|exists:seances,id',
            'user_id' => 'required|exists:users,id',
            'nbr_place' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $reservation = Reservation::create($request->all());
            return ToolsControlleur::successResponse(
                $reservation,
                'Réservation créée avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création réservation', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de la réservation : ' . $e->getMessage());
        }
    }

    // afficher une réservation spécifique
    public function show($id)
    {
        try {
            $reservation = Reservation::with(['seance', 'user'])->findOrFail($id);
            return ToolsControlleur::successResponse(
                $reservation,
                'Réservation récupérée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération réservation', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération de la réservation : ' . $e->getMessage());
        }
    }

    // mettre à jour une réservation
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'seance_id' => 'sometimes|exists:seances,id',
            'user_id' => 'sometimes|exists:users,id',
            'nbr_place' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->update($request->all());
            return ToolsControlleur::successResponse(
                $reservation,
                'Réservation mise à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour réservation', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour de la réservation : ' . $e->getMessage());
        }
    }

    // supprimer une réservation
    public function destroy($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();
            return ToolsControlleur::successResponse(
                null,
                'Réservation supprimée avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression réservation', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression de la réservation : ' . $e->getMessage());
        }
    }

}
