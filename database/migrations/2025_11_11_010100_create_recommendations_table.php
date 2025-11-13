<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->enum('subject_type', ['freelancer', 'company']);
            $table->unsignedBigInteger('subject_id');
            $table->enum('target_type', ['job', 'freelancer']);
            $table->unsignedBigInteger('target_id');
            $table->decimal('score', 5, 2);
            $table->date('batch_date');
            $table->enum('status', ['pending', 'viewed', 'liked', 'rejected', 'saved'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Index combinado (sem DESC explícito aqui). Para suportar DESC em MySQL 8, usamos DB::statement abaixo.
            $table->index(['subject_type', 'subject_id', 'status']);
        });

        // Tentar criar índice com ordem DESC para score quando suportado (MySQL 8+)
        try {
            DB::statement('CREATE INDEX recommendations_subject_status_score_desc ON recommendations (subject_type, subject_id, status, score DESC)');
        } catch (\Throwable $e) {
            // Silent fallback: mantém índice básico acima
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
