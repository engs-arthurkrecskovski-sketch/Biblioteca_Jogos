DROP TRIGGER IF EXISTS trg_atualiza_nota_jogo ON biblioteca_jogos.avaliacoes;
DROP TRIGGER IF EXISTS trg_log_biblioteca     ON biblioteca_jogos.biblioteca;

DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_atualiza_nota_jogo();
DROP FUNCTION IF EXISTS biblioteca_jogos.trg_fn_log_biblioteca();
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_total_horas_usuario(integer);
DROP FUNCTION IF EXISTS biblioteca_jogos.fn_ranking_jogos_genero(integer, integer);

CREATE FUNCTION biblioteca_jogos.fn_total_horas_usuario(p_id_usuario integer)
RETURNS numeric
LANGUAGE sql
STABLE
AS $$
    SELECT COALESCE(SUM(b.horas_jogadas), 0)::numeric
    FROM biblioteca_jogos.biblioteca b
    WHERE b.id_usuario = p_id_usuario;
$$;

CREATE FUNCTION biblioteca_jogos.fn_ranking_jogos_genero(p_id_genero integer, p_limite integer DEFAULT 10)
RETURNS TABLE (id_jogo integer, titulo text, nota_media numeric, total_avaliacoes integer)
LANGUAGE sql
STABLE
AS $$
    SELECT j.id_jogo::integer,
           j.titulo::text,
           j.nota_media::numeric,
           j.total_avaliacoes::integer
    FROM biblioteca_jogos.jogos j
    WHERE j.id_genero = p_id_genero
      AND j.nota_media IS NOT NULL
    ORDER BY j.nota_media DESC, j.total_avaliacoes DESC, j.titulo ASC
    LIMIT p_limite;
$$;