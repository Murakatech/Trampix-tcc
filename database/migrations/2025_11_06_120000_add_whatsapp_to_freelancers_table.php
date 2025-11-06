<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $t) {
            // Armazena apenas números (até 14 para segurança: DDI+DDD+número)
            $t->string('whatsapp', 20)->nullable()->after('portfolio_url');
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $t) {
            $t->dropColumn('whatsapp');
        });
    }
};