-- =====================================================
-- 01_estrutura.sql
-- Schema e tabelas da Biblioteca Digital de Jogos
-- Rodar PRIMEIRO, em banco vazio.
-- =====================================================
CREATE SCHEMA IF NOT EXISTS biblioteca_jogos;

SET search_path TO biblioteca_jogos;

CREATE TABLE biblioteca_jogos.desenvolvedoras (
    id_desenvolvedora  INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome               VARCHAR(150) NOT NULL,
    pais_origem        VARCHAR(60),
    ano_fundacao       INTEGER
);

CREATE TABLE biblioteca_jogos.generos (
    id_genero  INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome       VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE biblioteca_jogos.jogos (
    id_jogo            INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    titulo             VARCHAR(150) NOT NULL,
    descricao          TEXT,
    preco              NUMERIC(10,2) NOT NULL CHECK (preco >= 0),
    data_lancamento    DATE,
    id_desenvolvedora  INTEGER REFERENCES biblioteca_jogos.desenvolvedoras ON DELETE SET NULL,
    id_genero          INTEGER REFERENCES biblioteca_jogos.generos ON DELETE SET NULL,
    criado_em          TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE biblioteca_jogos.plataformas (
    id_plataforma  INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome           VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE biblioteca_jogos.jogos_plataformas (
    id_jogo        INTEGER NOT NULL REFERENCES biblioteca_jogos.jogos ON DELETE CASCADE,
    id_plataforma  INTEGER NOT NULL REFERENCES biblioteca_jogos.plataformas ON DELETE CASCADE,
    PRIMARY KEY (id_jogo, id_plataforma)
);

CREATE TABLE biblioteca_jogos.usuarios (
    id_usuario  INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    nome        VARCHAR(150) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    senha_hash  VARCHAR(255) NOT NULL,
    criado_em   TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE biblioteca_jogos.assinaturas (
    id_assinatura   INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_usuario      INTEGER NOT NULL REFERENCES biblioteca_jogos.usuarios ON DELETE CASCADE,
    plano           VARCHAR(20) NOT NULL DEFAULT 'gratuito'
                    CHECK (plano IN ('gratuito','premium','premium_anual')),
    data_inicio     DATE NOT NULL DEFAULT CURRENT_DATE,
    data_expiracao  DATE
);

CREATE TABLE biblioteca_jogos.biblioteca (
    id_usuario      INTEGER NOT NULL REFERENCES biblioteca_jogos.usuarios ON DELETE CASCADE,
    id_jogo         INTEGER NOT NULL REFERENCES biblioteca_jogos.jogos ON DELETE CASCADE,
    data_aquisicao  TIMESTAMP NOT NULL DEFAULT NOW(),
    horas_jogadas   NUMERIC(6,1) NOT NULL DEFAULT 0 CHECK (horas_jogadas >= 0),
    PRIMARY KEY (id_usuario, id_jogo)
);

CREATE TABLE biblioteca_jogos.avaliacoes (
    id_avaliacao   INTEGER GENERATED ALWAYS AS IDENTITY PRIMARY KEY,
    id_usuario     INTEGER NOT NULL REFERENCES biblioteca_jogos.usuarios ON DELETE CASCADE,
    id_jogo        INTEGER NOT NULL REFERENCES biblioteca_jogos.jogos ON DELETE CASCADE,
    nota           INTEGER NOT NULL CHECK (nota BETWEEN 1 AND 5),
    comentario     TEXT,
    data_avaliacao TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (id_usuario, id_jogo)
);
