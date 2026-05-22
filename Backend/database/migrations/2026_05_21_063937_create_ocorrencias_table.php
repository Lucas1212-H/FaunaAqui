<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ocorrencias', function (Blueprint $table) {
            // dados do denunciante
            $table->string('denunciante_nome');
            $table->string('denunciante_contato_tipo');
            $table->string('denunciante_contato_valor');
            $table->string('codigo_ocorrencia')->primary();
            // dados do animal
            $table->string('categoria_ocorrencia');
            $table->string('tipo_animal');
            $table->string('local_ocorrencia');
            $table->string('descricao_ocorrencia');
            //  Localização e Mídia 
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('ponto_referencia');
            $table->string('foto_path'); 

            
            $table->enum('status', ['Pendente', 'Em Atendimento', 'Resolvido', 'Falso Alarme'])->default('Pendente');
            $table->text('parecer_tecnico')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocorrencias');
    }
};
