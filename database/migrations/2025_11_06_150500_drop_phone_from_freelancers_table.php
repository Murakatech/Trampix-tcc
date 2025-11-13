<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('freelancers', 'phone')) {
            Schema::table('freelancers', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('freelancers', 'phone')) {
            Schema::table('freelancers', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('cv_url');
            });
        }
    }
};
