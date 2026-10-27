DROP TRIGGER IF EXISTS trg_atualiza_nota_jogo ON biblioteca_jogos.avaliacoes;
DROP TRIGGER IF EXISTS trg_log_biblioteca     ON biblioteca_jogos.biblioteca;

DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_atualiza_nota_jogo();
DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_log_biblioteca();
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_total_horas_usuario(integer);
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_ranking_jogos_genero(integer, integer);