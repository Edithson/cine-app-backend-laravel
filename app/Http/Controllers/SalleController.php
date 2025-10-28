<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            return PackageControlleur::successResponse(
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
            return PackageControlleur::successResponse(
                $salle,
                'Salle créée avec succès',
                201
            );
        } catch (\Exception $e) {
            \Log::error('Erreur création salle', ['error' => $e->getMessage()]);
            return ToolsControlleur::errorResponse('Erreur lors de la création de la salle : ' . $e->getMessage());
        }
    }

}
