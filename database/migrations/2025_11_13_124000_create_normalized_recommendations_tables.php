<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('freelancer_job_recommendations')) {
        Schema::create('freelancer_job_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('freelancers')->cascadeOnDelete();
            $table->foreignId('job_vacancy_id')->constrained('job_vacancies')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->date('batch_date');
            $table->enum('status', ['pending', 'viewed', 'liked', 'rejected', 'saved'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['freelancer_id', 'job_vacancy_id'], 'fjr_freelancer_job_unique');
        });
        }

        if (! Schema::hasTable('company_freelancer_recommendations')) {
        Schema::create('company_freelancer_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained('freelancers')->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->date('batch_date');
            $table->enum('status', ['pending', 'viewed', 'liked', 'rejected', 'saved'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['company_id', 'freelancer_id'], 'cfr_company_freelancer_unique');
        });
        }

        if (Schema::hasTable('recommendations')) {
            $rows = DB::table('recommendations')->select('*')->get();
            foreach ($rows as $r) {
                if ($r->subject_type === 'freelancer' && $r->target_type === 'job') {
                    DB::table('freelancer_job_recommendations')->updateOrInsert(
                        ['freelancer_id' => $r->subject_id, 'job_vacancy_id' => $r->target_id],
                        [
                            'score' => $r->score,
                            'batch_date' => $r->batch_date,
                            'status' => $r->status,
                            'decided_at' => $r->decided_at,
                            'created_at' => $r->created_at,
                        ]
                    );
                } elseif ($r->subject_type === 'company' && $r->target_type === 'freelancer') {
                    DB::table('company_freelancer_recommendations')->updateOrInsert(
                        ['company_id' => $r->subject_id, 'freelancer_id' => $r->target_id],
                        [
                            'score' => $r->score,
                            'batch_date' => $r->batch_date,
                            'status' => $r->status,
                            'decided_at' => $r->decided_at,
                            'created_at' => $r->created_at,
                        ]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_freelancer_recommendations');
        Schema::dropIfExists('freelancer_job_recommendations');
    }
};
