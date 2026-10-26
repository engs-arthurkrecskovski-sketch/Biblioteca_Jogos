SET search_path TO biblioteca_jogos; 


--views do 1bimestre 

CREATE OR REPLACE VIEW biblioteca_jogos.vw_jeferson_jogos_bem_avaliados AS
SELECT
    j.id_jogo,
    j.titulo,
    g.nome AS genero,
    d.nome AS desenvolvedora,
    j.preco,
    COUNT(a.id_avaliacao) AS total_avaliacoes,
    ROUND(AVG(a.nota), 2) AS media_nota
FROM biblioteca_jogos.jogos j
JOIN biblioteca_jogos.generos g ON g.id_genero = j.id_genero
JOIN biblioteca_jogos.desenvolvedoras d ON d.id_desenvolvedora = j.id_desenvolvedora
JOIN biblioteca_jogos.avaliacoes a ON a.id_jogo = j.id_jogo
GROUP BY j.id_jogo, j.titulo, g.nome, d.nome, j.preco
ORDER BY media_nota DESC, total_avaliacoes DESC;

CREATE OR REPLACE VIEW biblioteca_jogos.vw_jeferson_horas_por_plataforma AS
SELECT
    p.id_plataforma,
    p.nome AS plataforma,
    COUNT(DISTINCT jp.id_jogo) AS total_jogos,
    COALESCE(SUM(b.horas_jogadas), 0) AS total_horas_jogadas,
    ROUND(AVG(b.horas_jogadas), 2) AS media_horas_por_registro
FROM biblioteca_jogos.plataformas p
JOIN biblioteca_jogos.jogos_plataformas jp ON jp.id_plataforma = p.id_plataforma
LEFT JOIN biblioteca_jogos.biblioteca b ON b.id_jogo = jp.id_jogo
GROUP BY p.id_plataforma, p.nome
ORDER BY total_horas_jogadas DESC;

CREATE OR REPLACE VIEW biblioteca_jogos.vw_jeferson_assinaturas_resumo AS
SELECT
    ass.plano,
    COUNT(*) AS total_assinantes,
    COUNT(*) FILTER (WHERE ass.data_expiracao >= CURRENT_DATE) AS assinaturas_ativas,
    (
        SELECT ROUND(AVG(a.nota), 2)
        FROM biblioteca_jogos.avaliacoes a
        WHERE a.id_usuario IN (
            SELECT ass2.id_usuario
            FROM biblioteca_jogos.assinaturas ass2
            WHERE ass2.plano = ass.plano
        )
    ) AS media_nota_avaliacoes_do_plano
FROM biblioteca_jogos.assinaturas ass
GROUP BY ass.plano
ORDER BY total_assinantes DESC;

CREATE OR REPLACE VIEW biblioteca_jogos.vw_jeferson_usuarios_mais_ativos AS
SELECT
    u.id_usuario,
    u.nome,
    u.email,
    COALESCE(bib.total_jogos, 0) AS jogos_na_biblioteca,
    COALESCE(bib.total_horas, 0) AS total_horas_jogadas,
    COALESCE(av.total_avaliacoes, 0) AS total_avaliacoes
FROM biblioteca_jogos.usuarios u
LEFT JOIN (
    SELECT id_usuario, COUNT(*) AS total_jogos, SUM(horas_jogadas) AS total_horas
    FROM biblioteca_jogos.biblioteca
    GROUP BY id_usuario
) bib ON bib.id_usuario = u.id_usuario
LEFT JOIN (
    SELECT id_usuario, COUNT(*) AS total_avaliacoes
    FROM biblioteca_jogos.avaliacoes
    GROUP BY id_usuario
) av ON av.id_usuario = u.id_usuario
ORDER BY total_horas_jogadas DESC NULLS LAST;

CREATE MATERIALIZED VIEW IF NOT EXISTS biblioteca_jogos.vw_jeferson_desenvolvedoras_desempenho AS
SELECT
    d.id_desenvolvedora,
    d.nome AS desenvolvedora,
    d.pais_origem,
    COUNT(j.id_jogo) AS total_jogos,
    ROUND(AVG(j.preco), 2) AS preco_medio,
    (
        SELECT ROUND(AVG(a.nota), 2)
        FROM biblioteca_jogos.avaliacoes a
        JOIN biblioteca_jogos.jogos j2 ON j2.id_jogo = a.id_jogo
        WHERE j2.id_desenvolvedora = d.id_desenvolvedora
    ) AS media_nota_geral
FROM biblioteca_jogos.desenvolvedoras d
LEFT JOIN biblioteca_jogos.jogos j ON j.id_desenvolvedora = d.id_desenvolvedora
GROUP BY d.id_desenvolvedora, d.nome, d.pais_origem
ORDER BY total_jogos DESC;

-- INDICES
CREATE INDEX IF NOT EXISTS idx_jeferson_idjogo_avaliacoes
    ON biblioteca_jogos.avaliacoes (id_jogo);

CREATE INDEX IF NOT EXISTS idx_jeferson_idusuario_biblioteca
    ON biblioteca_jogos.biblioteca (id_usuario);