<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Categorie;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Categorie::class)->onDelete('cascade');
            $table->string('titre');
            $table->text('synopsis');
            $table->string('realisateur')->nullable();
            $table->date('date_sortie')->nullable();
            $table->string('langue');
            $table->string('classification')->nullable();
            $table->time('duree');
            $table->string('affiche');
            $table->string('bande_annonce')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
