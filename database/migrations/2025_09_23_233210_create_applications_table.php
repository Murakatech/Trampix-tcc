<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $t) {
            $t->id();
            $t->foreignId('job_vacancy_id')->constrained()->cascadeOnDelete();
            $t->foreignId('freelancer_id')->constrained()->cascadeOnDelete();
            $t->text('cover_letter')->nullable();
            $t->enum('status',['sent','review','accepted','rejected'])->default('sent');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
