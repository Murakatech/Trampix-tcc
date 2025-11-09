<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (Schema::hasColumn('freelancers', 'portfolio_url')) {
                $table->dropColumn('portfolio_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (!Schema::hasColumn('freelancers', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable();
            }
        });
    }
};