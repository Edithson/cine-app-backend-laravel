<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Seance;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'seance_id',
        'nbr_place',
    ];

    // Récupérer l'utilisateur associé à la réservation
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Récupérer la séance associée à la réservation
    public function seance()
    {
        return $this->belongsTo(Seance::class);
    }

}
