<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'segment_id')) {
                $table->foreignId('segment_id')
                    ->nullable()
                    ->constrained('segments')
                    ->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'segment_id')) {
                $table->dropForeign(['segment_id']);
                $table->dropColumn('segment_id');
            }
        });
    }
};
