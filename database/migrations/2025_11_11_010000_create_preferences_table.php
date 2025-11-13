<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['freelancer', 'company']);
            $table->json('desired_roles');
            $table->json('segments');
            $table->json('skills');
            $table->smallInteger('seniority_min');
            $table->smallInteger('seniority_max');
            $table->boolean('remote_ok')->default(false);
            $table->integer('salary_min')->nullable();
            $table->integer('salary_max')->nullable();
            $table->string('location')->nullable();
            $table->smallInteger('radius_km')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferences');
    }
};
