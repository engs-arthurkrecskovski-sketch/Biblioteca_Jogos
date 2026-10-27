# Biblioteca Digital de Jogos Eletrônicos

Projeto Avaliativo do 2º Bimestre (Banco de Dados II + Back-end) – Engenharia de Software, Centro Universitário Campo Real.

Aplicação web em **Laravel** com banco **PostgreSQL hospedado no Neon**. O banco é o do 1º bimestre
(schema `biblioteca_jogos`, ~97 mil registros - Jeferson), aprimorado com índices, functions, stored procedures e triggers.

## Tecnologias
- Laravel (MVC, Blade, Resource Controllers, API Resources, Services, Form Requests)
- PostgreSQL 18 no Neon
- PHPUnit (testes automatizados)
- Git/GitHub

## Integrantes
Preencher: Jeferson Malko, Arhur Luiz, Murilo Grando e Bruno Gabriel.

## Configuração do projeto

```bash
git clone <url-do-repositorio>
cd <pasta>
composer install
cp .env.example .env
php artisan key:generate
```

```env
DB_CONNECTION=pgsql
DB_HOST=ep-green-glade-acqzrgfx.sa-east-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=jeferson_blibliotecadejogos_db
DB_USERNAME=neondb_owner
DB_PASSWORD=
DB_SSLMODE=require

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> `SESSION_DRIVER`, `CACHE_STORE` e `QUEUE_CONNECTION` não podem ser `database`, para a aplicação
> não criar tabelas de sessão/cache dentro do banco do projeto.

Os Models usam o nome completo das tabelas (`biblioteca_jogos.jogos`), por isso **não é necessário** configurar `search_path`.

Rodar a aplicação:

```bash
php artisan serve
```

Acesse `http://localhost:8000`.

## Banco de dados

Scripts em `database/sql/` (ordem para recriar o banco do zero):

| Arquivo | Conteúdo |
|---|---|
| `01_estrutura.sql` | Schema e tabelas |
| `02_dados_parte1.sql` … `02_dados_parte4.sql` | Carga de dados (≈96 mil registros) |
| `03_views_e_indices.sql` | 5 views (1 materializada) e 2 índices |
| `04_objetos_functions_procedures_triggers.sql` | Colunas novas, tabela de log, 2 functions, 2 triggers e 2 stored procedures |
| `05_testes_objetos.sql` | Consultas usadas para testar os objetos |

Como o banco do Neon já está populado, **basta** aplicar o script 04 (ou rodar `php artisan migrate`, que o executa):

```bash
php artisan migrate
```

> Apague as migrations padrão do Laravel (`0001_01_01_*`) antes, pois o projeto usa as próprias migrations.
> As migrations do projeto são idempotentes (`IF NOT EXISTS` / `CREATE OR REPLACE`).

### Objetos do PostgreSQL e onde a aplicação os usa

| Objeto | Tipo | Uso na aplicação |
|---|---|---|
| `idx_jeferson_idjogo_avaliacoes` | Índice | Busca das avaliações de um jogo e recálculo da nota |
| `idx_jeferson_idusuario_biblioteca` | Índice | Consulta dos jogos de um usuário e total de horas |
| `fn_total_horas_usuario(id)` | Function | `BibliotecaService::totalHoras` → tela do usuário e `GET /api/usuarios/{id}/horas` |
| `fn_ranking_jogos_genero(id, limite)` | Function | `RankingService` → tela `/ranking` e `GET /api/generos/{id}/ranking` |
| `trg_atualiza_nota_jogo` | Trigger | Ao inserir/alterar/excluir avaliação, recalcula `nota_media` e `total_avaliacoes` do jogo |
| `trg_log_biblioteca` | Trigger | Ao inserir em `biblioteca`, grava `AQUISICAO` em `log_biblioteca` |
| `sp_adicionar_jogo_biblioteca(usuario, jogo)` | Procedure | `BibliotecaService::adicionarJogo` (valida e insere) |
| `sp_renovar_assinatura(usuario, plano)` | Procedure | `AssinaturaService::renovar` (30 dias premium, 365 premium_anual) |

### Backup e restauração
Arquivos em `database/backups/`. Comandos usados:

```bash
pg_dump -h <host> -U neondb_owner -d jeferson_blibliotecadejogos_db -Fc -f database/backups/backup_biblioteca.dump
pg_restore -h <host> -U neondb_owner -d <banco_reserva> -v -O --exit-on-error database/backups/backup_biblioteca.dump
```

A restauração foi feita em um banco reserva e as contagens das tabelas foram comparadas com o original.

## Rotas web

| Método | Rota | Descrição |
|---|---|---|
| GET | `/` | Início com totais |
| GET | `/jogos` | Lista jogos (busca por título, paginação) |
| GET/POST | `/jogos/create`, `/jogos` | Formulário e cadastro de jogo |
| GET | `/jogos/{jogo}` | Detalhes, nota média e avaliações |
| GET/PUT | `/jogos/{jogo}/edit`, `/jogos/{jogo}` | Edição de jogo |
| DELETE | `/jogos/{jogo}` | Exclusão de jogo |
| Resource | `/usuarios` | CRUD de usuários |
| GET | `/usuarios/{usuario}` | Biblioteca, assinatura, horas (function) e log (trigger) |
| POST | `/usuarios/{usuario}/biblioteca` | Adiciona jogo (procedure + trigger) |
| POST | `/usuarios/{usuario}/assinatura` | Troca o plano (procedure) |
| POST | `/avaliacoes` | Cria avaliação (dispara trigger da nota) |
| DELETE | `/avaliacoes/{avaliacao}` | Exclui avaliação (dispara trigger da nota) |
| GET | `/ranking?genero=ID` | Ranking por gênero (function) |

## Rotas da API (JSON)

| Método | Rota | Status | Descrição |
|---|---|---|---|
| GET | `/api/jogos?busca=&genero=&per_page=` | 200 | Lista paginada |
| GET | `/api/jogos/{id}` | 200 / 404 | Um jogo |
| POST | `/api/jogos` | 201 / 422 | Cria