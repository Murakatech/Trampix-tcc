<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('applications', function (Blueprint $table) {
            // Avaliação feita pela empresa sobre o freelancer
            $table->unsignedTinyInteger('company_rating')->nullable()->after('status');
            $table->text('company_comment')->nullable()->after('company_rating');
            $table->timestamp('evaluated_by_company_at')->nullable()->after('company_comment');

            // Avaliação feita pelo freelancer sobre a empresa
            $table->unsignedTinyInteger('freelancer_rating')->nullable()->after('evaluated_by_company_at');
            $table->text('freelancer_comment')->nullable()->after('freelancer_rating');
            $table->timestamp('evaluated_by_freelancer_at')->nullable()->after('freelancer_comment');

            // Reconhecimento de rejeição pelo freelancer (para ocultar aviso em "Minhas Candidaturas")
            $table->boolean('rejected_acknowledged')->default(false)->after('evaluated_by_freelancer_at');
        });
    }

    public function down(): void {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'company_rating',
                'company_comment',
                'evaluated_by_company_at',
                'freelancer_rating',
                'freelancer_comment',
                'evaluated_by_freelancer_at',
                'rejected_acknowledged',
            ]);
        });
    }
};