<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            // Remover o campo ENUM atual
            $table->dropColumn('availability');
        });

        Schema::table('freelancers', function (Blueprint $table) {
            // Adicionar novamente como TEXT
            $table->text('availability')->nullable()->after('hourly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            // Remover o campo TEXT
            $table->dropColumn('availability');
        });

        Schema::table('freelancers', function (Blueprint $table) {
            // Voltar para ENUM
            $table->enum('availability', ['available', 'busy', 'unavailable'])->default('available')->after('hourly_rate');
        });
    }
};
