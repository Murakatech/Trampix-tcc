<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('companies') || ! Schema::hasTable('sectors')) {
            return;
        }

        // Criar setores a partir dos textos existentes em companies.sector e vincular via pivot company_sector
        $companies = DB::table('companies')
            ->select('id', 'sector')
            ->whereNotNull('sector')
            ->where('sector', '<>', '')
            ->get();

        foreach ($companies as $comp) {
            $name = trim((string) $comp->sector);
            if ($name === '') continue;
            $existing = DB::table('sectors')->where('name', $name)->first();
            if (! $existing) {
                $slug = Str::slug($name);
                $base = $slug !== '' ? $slug : Str::slug('setor');
                $slugFinal = $base;
                $i = 1;
                while (DB::table('sectors')->where('slug', $slugFinal)->exists()) {
                    $slugFinal = $base.'-'.$i;
                    $i++;
                }
                DB::table('sectors')->insert([
                    'name' => $name,
                    'slug' => $slugFinal,
                    'icon' => null,
                    'is_active' => true,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $existing = DB::table('sectors')->where('slug', $slugFinal)->first();
            }

            if ($existing) {
                // Vincular no pivot se não existir
                $pivotExists = DB::table('company_sector')
                    ->where('company_id', $comp->id)
                    ->where('sector_id', $existing->id)
                    ->exists();
                if (! $pivotExists) {
                    DB::table('company_sector')->insert([
                        'company_id' => $comp->id,
                        'sector_id' => $existing->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Remover coluna textual redundante
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'sector')) {
                $table->dropColumn('sector');
            }
        });
    }

    public function down(): void
    {
        // Restaurar coluna textual para compatibilidade
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'sector')) {
                $table->string('sector')->nullable()->after('cnpj');
            }
        });
        // Opcional: repopular texto a partir dos setores vinculados
        try {
            $rows = DB::table('companies')->select('id')->get();
            foreach ($rows as $c) {
                $name = DB::table('company_sector')
                    ->join('sectors', 'company_sector.sector_id', '=', 'sectors.id')
                    ->where('company_sector.company_id', $c->id)
                    ->value('sectors.name');
                if ($name) {
                    DB::table('companies')->where('id', $c->id)->update(['sector' => $name]);
                }
            }
        } catch (\Throwable $e) {
        }
    }
};

