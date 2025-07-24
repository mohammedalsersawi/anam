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
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم الملف الاصلي');
            $table->string('filename')->comment('اسم الملف بعد التغير');
            $table->string('full_original_path')->comment('رابط الصور كامل');
            $table->string('path')->comment('المسار');
            $table->string('relation_id');
            $table->string('relation_type');
            $table->string('extension');
            $table->tinyInteger('type')->comment('1 = IMAGE, 2 = VIDEO');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
