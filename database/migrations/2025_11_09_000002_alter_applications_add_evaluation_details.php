<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Detalhes de avaliação por pergunta (JSON) e médias decimais
            $table->json('company_ratings_json')->nullable()->after('company_comment');
            $table->decimal('company_rating_avg', 3, 1)->nullable()->after('company_ratings_json');

            $table->json('freelancer_ratings_json')->nullable()->after('freelancer_comment');
            $table->decimal('freelancer_rating_avg', 3, 1)->nullable()->after('freelancer_ratings_json');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'company_ratings_json',
                'company_rating_avg',
                'freelancer_ratings_json',
                'freelancer_rating_avg',
            ]);
        });
    }
};
