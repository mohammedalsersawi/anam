<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('journey_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journey_section_id')
                ->constrained('journey_sections')
                ->onDelete('cascade');
            $table->text('feature');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journey_features');
    }
};
