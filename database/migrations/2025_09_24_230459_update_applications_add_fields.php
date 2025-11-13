<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // job_vacancy_id
        if (! Schema::hasColumn('applications', 'job_vacancy_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('job_vacancy_id')->after('id')->constrained()->onDelete('cascade');
            });
        }

        // freelancer_id
        if (! Schema::hasColumn('applications', 'freelancer_id')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->foreignId('freelancer_id')->after('job_vacancy_id')->constrained()->onDelete('cascade');
            });
        }

        // cover_letter
        if (! Schema::hasColumn('applications', 'cover_letter')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->text('cover_letter')->nullable()->after('freelancer_id');
            });
        }

        // status
        if (! Schema::hasColumn('applications', 'status')) {
            Schema::table('applications', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('cover_letter');
            });
        }
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('applications', 'cover_letter')) {
                $table->dropColumn('cover_letter');
            }
            if (Schema::hasColumn('applications', 'freelancer_id')) {
                $table->dropForeign(['freelancer_id']);
                $table->dropColumn('freelancer_id');
            }
            if (Schema::hasColumn('applications', 'job_vacancy_id')) {
                $table->dropForeign(['job_vacancy_id']);
                $table->dropColumn('job_vacancy_id');
            }
        });
    }
};
