<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_sector', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sector_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['freelancer_id', 'sector_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_sector');
    }
};
