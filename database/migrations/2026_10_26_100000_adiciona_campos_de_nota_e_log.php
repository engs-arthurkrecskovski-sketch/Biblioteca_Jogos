<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
ALTER TABLE biblioteca_jogos.jogos
    ADD COLUMN IF NOT EXISTS nota_media NUMERIC(3,2),
    ADD COLUMN IF NOT EXISTS total_avaliacoes INTEGER NOT NULL DEFAULT 0;

CREATE TABLE IF NOT EXISTS biblioteca_jogos.log_biblioteca (
    id_log      INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_usuario  INTEGER NOT NULL,
    id_jogo     INTEGER NOT NULL,
    acao        VARCHAR(30) NOT NULL,
    data_acao   TIMESTAMP NOT NULL DEFAULT NOW()
);
SQL);
    }

    public function down(): void
    {
        DB::unprepared(<<<'SQL'
DROP TABLE IF EXISTS biblioteca_jogos.log_biblioteca;
ALTER TABLE biblioteca_jogos.jogos
    DROP COLUMN IF EXISTS nota_media,
    DROP COLUMN IF EXISTS total_avaliacoes;
SQL);
    }
};