<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Film;
use App\Models\Salle;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'film_id',
        'salle_id',
        'date_heure_debut',
        'date_heure_fin',
        'note',
        'pu_place',
    ];

    // Récupérer le film associé à la séance
    public function film()
    {
        return $this->belongsTo(Film::class);
    }

    // Récupérer la salle associée à la séance
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    // Récupérer les réservations associées à la séance
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

}
