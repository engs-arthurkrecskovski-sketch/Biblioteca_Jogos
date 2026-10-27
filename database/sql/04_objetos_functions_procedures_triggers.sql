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

CREATE OR REPLACE PROCEDURE biblioteca_jogos.sp_adicionar_jogo_biblioteca(p_id_usuario integer, p_id_jogo integer)
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM biblioteca_jogos.usuarios u WHERE u.id_usuario = p_id_usuario) THEN
        RAISE EXCEPTION 'Usuário % não existe.', p_id_usuario;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM biblioteca_jogos.jogos j WHERE j.id_jogo = p_id_jogo) THEN
        RAISE EXCEPTION 'Jogo % não existe.', p_id_jogo;
    END IF;

    IF EXISTS (SELECT 1 FROM biblioteca_jogos.biblioteca b
                WHERE b.id_usuario = p_id_usuario AND b.id_jogo = p_id_jogo) THEN
        RAISE EXCEPTION 'O jogo já está na biblioteca deste usuário.';
    END IF;

    INSERT INTO biblioteca_jogos.biblioteca (id_usuario, id_jogo)
    VALUES (p_id_usuario, p_id_jogo);
END;
$$;

CREATE OR REPLACE PROCEDURE biblioteca_jogos.sp_renovar_assinatura(p_id_usuario integer, p_plano varchar)
LANGUAGE plpgsql
AS $$
DECLARE
    v_dias      integer;
    v_expira    date;
    v_id_assin  integer;
BEGIN
    IF NOT EXISTS (SELECT 1 FROM biblioteca_jogos.usuarios u WHERE u.id_usuario = p_id_usuario) THEN
        RAISE EXCEPTION 'Usuário % não existe.', p_id_usuario;
    END IF;

    IF p_plano NOT IN ('gratuito', 'premium', 'premium_anual') THEN
        RAISE EXCEPTION 'Plano inválido: %. Use gratuito, premium ou premium_anual.', p_plano;
    END IF;

    v_dias := CASE p_plano WHEN 'premium' THEN 30 WHEN 'premium_anual' THEN 365 ELSE NULL END;
    v_expira := CASE WHEN v_dias IS NULL THEN NULL ELSE CURRENT_DATE + v_dias END;

    SELECT a.id_assinatura INTO v_id_assin
      FROM biblioteca_jogos.assinaturas a
     WHERE a.id_usuario = p_id_usuario
     ORDER BY a.id_assinatura DESC
     LIMIT 1;

    IF v_id_assin IS NULL THEN
        INSERT INTO biblioteca_jogos.assinaturas (id_usuario, plano, data_inicio, data_expiracao)
        VALUES (p_id_usuario, p_plano, CURRENT_DATE, v_expira);
    ELSE
        UPDATE biblioteca_jogos.assinaturas
           SET plano = p_plano,
               data_inicio = CURRENT_DATE,
               data_expiracao = v_expira
         WHERE id_assinatura = v_id_assin;
    END IF;
END;
$$;