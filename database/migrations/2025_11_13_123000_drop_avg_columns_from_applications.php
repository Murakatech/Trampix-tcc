<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'company_rating_avg')) {
                $table->dropColumn('company_rating_avg');
            }
            if (Schema::hasColumn('applications', 'freelancer_rating_avg')) {
                $table->dropColumn('freelancer_rating_avg');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'company_rating_avg')) {
                $table->decimal('company_rating_avg', 3, 1)->nullable()->after('company_ratings_json');
            }
            if (! Schema::hasColumn('applications', 'freelancer_rating_avg')) {
                $table->decimal('freelancer_rating_avg', 3, 1)->nullable()->after('freelancer_ratings_json');
            }
        });
    }
};

