<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Film;
use App\Http\Controllers\ToolsControlleur;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class FilmsController extends Controller
{
    // Récupérer tous les films avec pagination
    public function index(Request $request)
    {
        try {
            // Récupération de tous les films avec leurs catégories associées
            $films = Film::with('categorie')->paginate($request->input('per_page', 10));
            return ToolsControlleur::successResponse(
                $films,
                'Liste des films récupérée avec succès',
                ['count' => $films->count()],
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération films', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération des films : ' . $e->getMessage());
        }
    }

    // Création d'un film
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'synopsis' => 'nullable|string',
            'realisateur' => 'nullable|string',
            'date_sortie' => 'required|date',
            'langue' => 'required|string',
            'classification' => 'required|string',
            'duree' => 'required|time',
            'affiche' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bande_annonce' => 'nullable|video|mimes:mp4,avi,mov|max:10240',
            'categorie_id' => 'required|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }
        try {
            // Gestion de l'upload de l'affiche
            if ($request->hasFile('affiche')) {
                $affichePath = $request->file('affiche')->store('affiches', 'public');
                $request->merge(['affiche' => $affichePath]);
            }

            // Gestion de l'upload de la bande annonce
            if ($request->hasFile('bande_annonce')) {
                $bandeAnnoncePath = $request->file('bande_annonce')->store('bandes_annonces', 'public');
                $request->merge(['bande_annonce' => $bandeAnnoncePath]);
            }

            $film = Film::create($request->all());
            return ToolsControlleur::successResponse(
                $film,
                'Film créé avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création film', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création du film : ' . $e->getMessage());
        }
    }

    // Récupérer un film par son ID (avec la catégorie associée et ses séances) ainsi que les avis et la note moyenne
    public function show($id)
    {
        try {
            $film = Film::with(['categorie', 'seances', 'avis'])->findOrFail($id);
            $noteMoyenne = $film->avis->avg('note');
            $film->note_moyenne = $noteMoyenne;
            return ToolsControlleur::successResponse(
                $film,
                'Film récupéré avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur récupération film', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la récupération du film : ' . $e->getMessage());
        }
    }

    // Mettre à jour un film
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'sometimes|required|string|max:255',
            'synopsis' => 'nullable|string',
            'realisateur' => 'nullable|string',
            'date_sortie' => 'sometimes|required|date',
            'langue' => 'sometimes|required|string',
            'classification' => 'sometimes|required|string',
            'duree' => 'sometimes|required|time',
            'affiche' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bande_annonce' => 'nullable|video|mimes:mp4,avi,mov|max:10240',
            'categorie_id' => 'sometimes|required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return ToolsControlleur::errorResponse('Validation failed : '.$validator->errors(), $validator->errors());
        }

        try {
            $film = Film::with(['affiche', 'bande_annonce'])->findOrFail($id);

            // Gestion de l'upload de l'affiche
            if ($request->hasFile('affiche')) {
                // suppression de l'ancienne affiche si elle existe
                if ($film->affiche) {
                    Storage::disk('public')->delete($film->affiche);
                }
                $affichePath = $request->file('affiche')->store('affiches', 'public');
                $request->merge(['affiche' => $affichePath]);
            }

            // Gestion de l'upload de la bande annonce
            if ($request->hasFile('bande_annonce')) {
                // suppression de l'ancienne bande annonce si elle existe
                if ($film->bande_annonce) {
                    Storage::disk('public')->delete($film->bande_annonce);
                }
                $bandeAnnoncePath = $request->file('bande_annonce')->store('bandes_annonces', 'public');
                $request->merge(['bande_annonce' => $bandeAnnoncePath]);
            }

            $film->update($request->all());
            return ToolsControlleur::successResponse(
                $film,
                'Film mis à jour avec succès',
                200
            );
        } catch (\Exception $e) {
            \Log::error('Erreur mise à jour film', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la mise à jour du film : ' . $e->getMessage());
        }
    }

    // Supprimer un film et des seances et ressources associées
    public function destroy($id)
    {
        try {
            $film = Film::with(['affiche', 'bande_annonce', 'seances'])->findOrFail($id);

            // Suppression des séances associées
            foreach ($film->seances as $seance) {
                // suppression de la séance et de ses reservations associées
                foreach ($seance->reservations as $reservation) {
                    $reservation->delete();
                }
                $seance->delete();
            }

            // Suppression de l'affiche si elle existe
            if ($film->affiche) {
                Storage::disk('public')->delete($film->affiche);
            }

            // Suppression de la bande annonce si elle existe
            if ($film->bande_annonce) {
                Storage::disk('public')->delete($film->bande_annonce);
            }

            $film->delete();
            return ToolsControlleur::successResponse(
                null,
                'Film supprimé avec succès',
                204
            );
        } catch (\Exception $e) {
            \Log::error('Erreur suppression film', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la suppression du film : ' . $e->getMessage());
        }
    }

}
