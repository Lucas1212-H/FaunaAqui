<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE ocorrencias DROP PRIMARY KEY, ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        DB::statement('ALTER TABLE ocorrencias DROP COLUMN codigo_ocorrencia');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE ocorrencias ADD COLUMN codigo_ocorrencia VARCHAR(255) NOT NULL FIRST');
        DB::statement('ALTER TABLE ocorrencias DROP PRIMARY KEY, ADD PRIMARY KEY (codigo_ocorrencia)');
        DB::statement('ALTER TABLE ocorrencias DROP COLUMN id');
    }
};
