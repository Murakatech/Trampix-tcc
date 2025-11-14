<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (! Schema::hasColumn('job_vacancies', 'salary_min')) {
                $table->decimal('salary_min', 10, 2)->nullable()->after('location_type');
            }
            if (! Schema::hasColumn('job_vacancies', 'salary_max')) {
                $table->decimal('salary_max', 10, 2)->nullable()->after('salary_min');
            }
        });

        // Backfill a partir de salary_range textual
        $rows = DB::table('job_vacancies')
            ->select('id', 'salary_range')
            ->where(function ($q) {
                $q->whereNull('salary_min')->orWhereNull('salary_max');
            })
            ->get();
        foreach ($rows as $r) {
            $text = (string) ($r->salary_range ?? '');
            if ($text === '') continue;
            $m = [];
            preg_match_all('/(\d+[\.,]?\d*)/u', $text, $m);
            $nums = [];
            foreach (($m[1] ?? []) as $raw) {
                $clean = (float) str_replace([',', '.'], '', $raw);
                if ($clean > 0) $nums[] = $clean;
            }
            if (count($nums) >= 2) {
                sort($nums);
                DB::table('job_vacancies')->where('id', $r->id)->update([
                    'salary_min' => $nums[0],
                    'salary_max' => $nums[1],
                ]);
            } elseif (count($nums) === 1) {
                DB::table('job_vacancies')->where('id', $r->id)->update([
                    'salary_min' => $nums[0] * 0.9,
                    'salary_max' => $nums[0] * 1.1,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('job_vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('job_vacancies', 'salary_min')) {
                $table->dropColumn('salary_min');
            }
            if (Schema::hasColumn('job_vacancies', 'salary_max')) {
                $table->dropColumn('salary_max');
            }
        });
    }
};

