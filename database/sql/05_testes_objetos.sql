BEGIN;

INSERT INTO biblioteca_jogos.usuarios (nome, email, senha_hash)
VALUES ('Usuario Teste SQL', 'teste_sql_' || floor(random()*1e9)::text || '@teste.dev', 'x');

INSERT INTO biblioteca_jogos.jogos (titulo, preco)
VALUES ('Jogo Teste SQL', 10);

DO $$
DECLARE
    v_usuario integer := (SELECT MAX(id_usuario) FROM biblioteca_jogos.usuarios);
    v_jogo    integer := (SELECT MAX(id_jogo)    FROM biblioteca_jogos.jogos);
BEGIN
    CALL biblioteca_jogos.sp_adicionar_jogo_biblioteca(v_usuario, v_jogo);
END $$;

SELECT * FROM biblioteca_jogos.log_biblioteca ORDER BY id_log DESC LIMIT 3;

SELECT biblioteca_jogos.fn_total_horas_usuario((SELECT MAX(id_usuario) FROM biblioteca_jogos.usuarios)) AS total_horas;

SELECT id_jogo, nota_media, total_avaliacoes
  FROM biblioteca_jogos.jogos WHERE id_jogo = (SELECT MAX(id_jogo) FROM biblioteca_jogos.jogos);

INSERT INTO biblioteca_jogos.avaliacoes (id_usuario, id_jogo, nota, comentario)
VALUES ((SELECT MAX(id_usuario) FROM biblioteca_jogos.usuarios),
        (SELECT MAX(id_jogo)    FROM biblioteca_jogos.jogos), 5, 'teste');

SELECT id_jogo, nota_media, total_avaliacoes 
  FROM biblioteca_jogos.jogos WHERE id_jogo = (SELECT MAX(id_jogo) FROM biblioteca_jogos.jogos);

DO $$
DECLARE
    v_usuario integer := (SELECT MAX(id_usuario) FROM biblioteca_jogos.usuarios);
BEGIN
    CALL biblioteca_jogos.sp_renovar_assinatura(v_usuario, 'premium');
END $$;

SELECT * FROM biblioteca_jogos.assinaturas
 WHERE id_usuario = (SELECT MAX(id_usuario) FROM biblioteca_jogos.usuarios);

SELECT * FROM biblioteca_jogos.fn_ranking_jogos_genero(1, 5);

ROLLBACK;