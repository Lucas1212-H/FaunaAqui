<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE ocorrencias MODIFY status ENUM('Pendente', 'Em Atendimento', 'Resolvido', 'Falso Alarme', 'Publicado') NOT NULL DEFAULT 'Pendente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE ocorrencias MODIFY status ENUM('Pendente', 'Em Atendimento', 'Resolvido', 'Falso Alarme') NOT NULL DEFAULT 'Pendente'");
    }
};
