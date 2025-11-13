<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freelancer_skill', function (Blueprint $t) {
            $t->foreignId('freelancer_id')->constrained()->cascadeOnDelete();
            $t->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $t->primary(['freelancer_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_skill');
    }
};
