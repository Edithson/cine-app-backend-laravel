<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Films;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categorie extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom',
        'description',
    ];

    // Récupérer les films associés à la catégorie
    public function films()
    {
        return $this->hasMany(Films::class);
    }

}
