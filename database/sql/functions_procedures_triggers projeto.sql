-- functions, triggers e procedures

-- estrutura nova
ALTER TABLE biblioteca_jogos.jogos
    ADD COLUMN IF NOT EXISTS nota_media NUMERIC(3,2),
    ADD COLUMN IF NOT EXISTS total_avaliacoes INTEGER NOT NULL DEFAULT 0;

-- tabela de log
CREATE TABLE IF NOT EXISTS biblioteca_jogos.log_biblioteca (
    id_log      INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_usuario  INTEGER NOT NULL,
    id_jogo     INTEGER NOT NULL,
    acao        VARCHAR(30) NOT NULL,
    data_acao   TIMESTAMP NOT NULL DEFAULT NOW()
);

-- carga inicial das notas
UPDATE biblioteca_jogos.jogos j
SET nota_media = s.media,
    total_avaliacoes = s.total
FROM (
    SELECT id_jogo, ROUND(AVG(nota), 2) AS media, COUNT(*) AS total
    FROM biblioteca_jogos.avaliacoes
    GROUP BY id_jogo
) s
WHERE s.id_jogo = j.id_jogo;

-- functions
-- function 1: total de horas do usuario
CREATE OR REPLACE FUNCTION biblioteca_jogos.fn_total_horas_usuario(p_id_usuario INTEGER)
RETURNS NUMERIC
LANGUAGE sql
STABLE
AS $$
    SELECT COALESCE(SUM(horas_jogadas), 0)
    FROM biblioteca_jogos.biblioteca
    WHERE id_usuario = p_id_usuario;
$$;

-- function 2: ranking de jogos por genero
CREATE OR REPLACE FUNCTION biblioteca_jogos.fn_ranking_jogos_genero(
    p_id_genero INTEGER,
    p_limite INTEGER DEFAULT 10
)
RETURNS TABLE (
    id_jogo INTEGER,
    titulo VARCHAR,
    nota_media NUMERIC,
    total_avaliacoes INTEGER
)
LANGUAGE sql
STABLE
AS $$
    SELECT j.id_jogo, j.titulo, j.nota_media, j.total_avaliacoes
    FROM biblioteca_jogos.jogos j
    WHERE j.id_genero = p_id_genero
      AND j.nota_media IS NOT NULL
    ORDER BY j.nota_media DESC, j.total_avaliacoes DESC
    LIMIT p_limite;
$$;

-- triggers
-- trigger 1: atualiza nota do jogo
CREATE OR REPLACE FUNCTION biblioteca_jogos.trg_fn_atualiza_nota_jogo()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
DECLARE
    v_id_jogo INTEGER;
BEGIN
    v_id_jogo := COALESCE(NEW.id_jogo, OLD.id_jogo);

    UPDATE biblioteca_jogos.jogos
    SET nota_media = (SELECT ROUND(AVG(nota), 2)
                      FROM biblioteca_jogos.avaliacoes
                      WHERE id_jogo = v_id_jogo),
        total_avaliacoes = (SELECT COUNT(*)
                            FROM biblioteca_jogos.avaliacoes
                            WHERE id_jogo = v_id_jogo)
    WHERE id_jogo = v_id_jogo;

    RETURN NULL;
END;
$$;

DROP TRIGGER IF EXISTS trg_atualiza_nota_jogo ON biblioteca_jogos.avaliacoes;
CREATE TRIGGER trg_atualiza_nota_jogo
AFTER INSERT OR UPDATE OR DELETE ON biblioteca_jogos.avaliacoes
FOR EACH ROW
EXECUTE FUNCTION biblioteca_jogos.trg_fn_atualiza_nota_jogo();

-- trigger 2: log da biblioteca
CREATE OR REPLACE FUNCTION biblioteca_jogos.trg_fn_log_biblioteca()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    INSERT INTO biblioteca_jogos.log_biblioteca (id_usuario, id_jogo, acao)
    VALUES (NEW.id_usuario, NEW.id_jogo, 'AQUISICAO');
    RETURN NEW;
END;
$$;

DROP TRIGGER IF EXISTS trg_log_biblioteca ON biblioteca_jogos.biblioteca;
CREATE TRIGGER trg_log_biblioteca
AFTER INSERT ON biblioteca_jogos.biblioteca
FOR EACH ROW
EXECUTE FUNCTION biblioteca_jogos.trg_fn_log_biblioteca();

-- procedures
-- procedure 1: adicionar jogo na biblioteca
CREATE OR REPLACE PROCEDURE biblioteca_jogos.sp_adicionar_jogo_biblioteca(
    p_id_usuario INTEGER,
    p_id_jogo INTEGER
)
LANGUAGE plpgsql
AS $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM biblioteca_jogos.usuarios WHERE id_usuario = p_id_usuario) THEN
        RAISE EXCEPTION 'Usuario % nao existe', p_id_usuario;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM biblioteca_jogos.jogos WHERE id_jogo = p_id_jogo) THEN
        RAISE EXCEPTION 'Jogo % nao existe', p_id_jogo;
    END IF;

    IF EXISTS (SELECT 1 FROM biblioteca_jogos.biblioteca
               WHERE id_usuario = p_id_usuario AND id_jogo = p_id_jogo) THEN
        RAISE EXCEPTION 'Usuario % ja possui o jogo %', p_id_usuario, p_id_jogo;
    END IF;

    INSERT INTO biblioteca_jogos.biblioteca (id_usuario, id_jogo)
    VALUES (p_id_usuario, p_id_jogo);
END;
$$;

-- procedure 2: renovar assinatura
CREATE OR REPLACE PROCEDURE biblioteca_jogos.sp_renovar_assinatura(
    p_id_usuario INTEGER,
    p_plano VARCHAR
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_dias INTEGER;
BEGIN
    IF p_plano NOT IN ('gratuito', 'premium', 'premium_anual') THEN
        RAISE EXCEPTION 'Plano invalido: %', p_plano;
    END IF;

    v_dias := CASE p_plano
                  WHEN 'premium' THEN 30
                  WHEN 'premium_anual' THEN 365
                  ELSE NULL
              END;

    IF EXISTS (SELECT 1 FROM biblioteca_jogos.assinaturas WHERE id_usuario = p_id_usuario) THEN
        UPDATE biblioteca_jogos.assinaturas
        SET plano = p_plano,
            data_inicio = CURRENT_DATE,
            data_expiracao = CASE WHEN v_dias IS NULL THEN NULL
                                  ELSE CURRENT_DATE + v_dias END
        WHERE id_usuario = p_id_usuario;
    ELSE
        INSERT INTO biblioteca_jogos.assinaturas (id_usuario, plano, data_expiracao)
        VALUES (p_id_usuario, p_plano,
                CASE WHEN v_dias IS NULL THEN NULL ELSE CURRENT_DATE + v_dias END);
    END IF;
END;
$$;