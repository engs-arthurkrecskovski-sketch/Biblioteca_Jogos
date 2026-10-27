<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $arquivo = database_path('sql/04_objetos_functions_procedures_triggers.sql');

        DB::unprepared(file_get_contents($arquivo));
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS trg_atualiza_nota_jogo ON biblioteca_jogos.avaliacoes;
DROP TRIGGER IF EXISTS trg_log_biblioteca ON biblioteca_jogos.biblioteca;
DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_atualiza_nota_jogo();
DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_log_biblioteca();
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_total_horas_usuario(integer);
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_ranking_jogos_genero(integer, integer);
DROP PROCEDURE IF EXISTS biblioteca_jogos.sp_adicionar_jogo_biblioteca(integer, integer);
DROP PROCEDURE IF EXISTS biblioteca_jogos.sp_renovar_assinatura(integer, varchar);
SQL);
    }
};