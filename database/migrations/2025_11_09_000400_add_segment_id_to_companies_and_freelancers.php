<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'segment_id')) {
                $table->foreignId('segment_id')
                    ->nullable()
                    ->constrained('segments')
                    ->nullOnDelete();
            }
        });

        Schema::table('freelancers', function (Blueprint $table) {
            if (! Schema::hasColumn('freelancers', 'segment_id')) {
                $table->foreignId('segment_id')
                    ->nullable()
                    ->constrained('segments')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'segment_id')) {
                $table->dropConstrainedForeignId('segment_id');
            }
        });

        Schema::table('freelancers', function (Blueprint $table) {
            if (Schema::hasColumn('freelancers', 'segment_id')) {
                $table->dropConstrainedForeignId('segment_id');
            }
        });
    }
};
