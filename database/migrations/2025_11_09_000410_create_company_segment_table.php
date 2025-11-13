<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_segment')) {
            Schema::create('company_segment', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
                $table->foreignId('segment_id')->constrained('segments')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['company_id', 'segment_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('company_segment');
    }
};
