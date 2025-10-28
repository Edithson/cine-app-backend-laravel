<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Films;
use App\Models\Salle;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Films::class)->onDelete('cascade');
            $table->foreignIdFor(Salle::class)->onDelete('cascade');
            $table->timestamp('date_heure_debut');
            $table->timestamp('date_heure_fin');
            $table->string('note')->nullable();
            $table->integer('pu_place');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seances');
    }
};
