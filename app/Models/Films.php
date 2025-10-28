<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Seance;
use App\Models\Avis;
use App\Models\Category;
use Illuminate\Database\Eloquent\SoftDeletes;

class Films extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titre',
        'synopsis',
        'realisateur',
        'date_sortie',
        'langue',
        'classification',
        'duree',
        'affiche',
        'bande_annonce',
    ];

    // Récupérer les séances associées au film
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

    // Récupérer les avis associés au film
    public function avis()
    {
        return $this->hasMany(Avis::class);
    }

    // Récuperer la catégorie du film
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Calculer la note moyenne du film
    public function averageRating()
    {
        return $this->avis()->avg('note');
    }

}
