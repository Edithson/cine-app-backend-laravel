<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Films;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'film_id',
        'user_id',
        'note',
        'commentaire',
    ];

    // Récupérer le film associé à l'avis
    public function film()
    {
        return $this->belongsTo(Films::class);
    }

    // Récupérer l'utilisateur associé à l'avis
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
