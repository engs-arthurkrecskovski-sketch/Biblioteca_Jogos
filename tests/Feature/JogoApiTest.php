<?php

namespace Tests\Feature;

use App\Models\Desenvolvedora;
use App\Models\Genero;
use App\Models\Jogo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
class JogoApiTest extends TestCase
{
    use DatabaseTransactions;

private function dadosJogo(array $extra = []): array
{
    return array_merge([
        'titulo' => 'Jogo API ' . uniqid(),
        'descricao' => 'Criado pelo teste automatizado',
        'preco' => 49.90,
        'data_lancamento' => '2026-01-15',
        'id_genero' => Genero::query()->value('id_genero'),
        'id_desenvolvedora' => Desenvolvedora::query()->value('id_desenvolvedora'),
    ], $extra);
}

$resposta = $this->getJson('/api/jogos?per_page=5');

$resposta->assertOk();
$resposta->assertJsonStructure([...]);
$this->assertLessThanOrEqual(5, count($resposta->json('data')));
    }