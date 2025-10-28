<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seance;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'adresse',
        'capacite',
    ];

    // Récupérer les séances associées à la salle
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }
}
