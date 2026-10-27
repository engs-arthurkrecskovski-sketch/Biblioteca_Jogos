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

CREATE FUNCTION biblioteca_jogos.trg_fn_atualiza_nota_jogo()
RETURNS trigger
LANGUAGE plpgsql
AS $$
DECLARE
    v_jogo integer;
BEGIN
    IF TG_OP IN ('INSERT', 'UPDATE') THEN
        v_jogo := NEW.id_jogo;
        UPDATE biblioteca_jogos.jogos j
           SET nota_media = (SELECT ROUND(AVG(a.nota)::numeric, 2)
                               FROM biblioteca_jogos.avaliacoes a
                              WHERE a.id_jogo = v_jogo),
               total_avaliacoes = (SELECT COUNT(*)
                                     FROM biblioteca_jogos.avaliacoes a
                                    WHERE a.id_jogo = v_jogo)
         WHERE j.id_jogo = v_jogo;
    END IF;

    IF TG_OP = 'DELETE' OR (TG_OP = 'UPDATE' AND OLD.id_jogo IS DISTINCT FROM NEW.id_jogo) THEN
        v_jogo := OLD.id_jogo;
        UPDATE biblioteca_jogos.jogos j
           SET nota_media = (SELECT ROUND(AVG(a.nota)::numeric, 2)
                               FROM biblioteca_jogos.avaliacoes a
                              WHERE a.id_jogo = v_jogo),
               total_avaliacoes = (SELECT COUNT(*)
                                     FROM biblioteca_jogos.avaliacoes a
                                    WHERE a.id_jogo = v_jogo)
         WHERE j.id_jogo = v_jogo;
    END IF;

    RETURN NULL;
END;
$$;

CREATE TRIGGER trg_atualiza_nota_jogo
AFTER INSERT OR UPDATE OR DELETE ON biblioteca_jogos.avaliacoes
FOR EACH ROW
EXECUTE FUNCTION biblioteca_jogos.trg_fn_atualiza_nota_jogo();

CREATE FUNCTION biblioteca_jogos.trg_fn_log_biblioteca()
RETURNS trigger
LANGUAGE plpgsql
AS $$
BEGIN
    INSERT INTO biblioteca_jogos.log_biblioteca (id_usuario, id_jogo, acao)
    VALUES (NEW.id_usuario, NEW.id_jogo, 'AQUISICAO');

    RETURN NULL;
END;
$$;

CREATE TRIGGER trg_log_biblioteca
AFTER INSERT ON biblioteca_jogos.biblioteca
FOR EACH ROW
EXECUTE FUNCTION biblioteca_jogos.trg_fn_log_biblioteca();
