SET search_path TO biblioteca_jogos;


-- testes dos objetos

-- function 1
SELECT biblioteca_jogos.fn_total_horas_usuario(1);

-- function 2
SELECT * FROM biblioteca_jogos.fn_ranking_jogos_genero(1, 5);

-- trigger 1: antes
SELECT id_jogo, nota_media, total_avaliacoes
FROM biblioteca_jogos.jogos WHERE id_jogo = 457;

-- trigger 1: insert
INSERT INTO biblioteca_jogos.avaliacoes (id_usuario, id_jogo, nota, comentario)
VALUES (1, 457, 5, 'Teste do trigger');

-- trigger 1: depois
SELECT id_jogo, nota_media, total_avaliacoes
FROM biblioteca_jogos.jogos WHERE id_jogo = 457;

-- trigger 1: limpar teste
DELETE FROM biblioteca_jogos.avaliacoes WHERE id_usuario = 1 AND id_jogo = 457;

-- achar usuario e jogo livres
SELECT u.id_usuario, j.id_jogo
FROM biblioteca_jogos.usuarios u
CROSS JOIN biblioteca_jogos.jogos j
WHERE NOT EXISTS (
    SELECT 1 FROM biblioteca_jogos.biblioteca b
    WHERE b.id_usuario = u.id_usuario AND b.id_jogo = j.id_jogo
)
LIMIT 1;

-- procedure 1 + trigger 2 (trocar pelos numeros acima)
CALL biblioteca_jogos.sp_adicionar_jogo_biblioteca(1, 2);
SELECT * FROM biblioteca_jogos.log_biblioteca ORDER BY id_log DESC LIMIT 5;


-- procedure 2
CALL biblioteca_jogos.sp_renovar_assinatura(1, 'premium_anual');
SELECT * FROM biblioteca_jogos.assinaturas WHERE id_usuario = 1;


-- indices
SELECT indexname, tablename, indexdef
FROM pg_indexes
WHERE schemaname = 'biblioteca_jogos'
  AND indexname LIKE 'idx_jeferson%';