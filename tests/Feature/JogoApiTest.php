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
public function test_get_jogos_retorna_200_e_estrutura_json(): void
    {

$resposta = $this->getJson('/api/jogos?per_page=5');

$resposta->assertOk();
$resposta->assertJsonStructure([...]);
$this->assertLessThanOrEqual(5, count($resposta->json('data')));

$dados = $this->dadosJogo();
$resposta = $this->postJson('/api/jogos', $dados);

$resposta->assertCreated();
$resposta->assertJsonStructure(['data' => [...]]);
$resposta->assertJsonPath('data.titulo', $dados['titulo']);
$this->assertTrue(Jogo::where('titulo', $dados['titulo'])->exists());   
    }
}
public function test_post_jogos_invalido_retorna_422(): void
    {
        $resposta = $this->postJson('/api/jogos', ['titulo' => '']);

        $resposta->assertStatus(422);
        $resposta->assertJsonValidationErrors(['titulo', 'preco']);
    }
    public function test_get_jogo_inexistente_retorna_404(): void
    {
        $this->getJson('/api/jogos/999999999')->assertNotFound();
    }
    public function test_put_atualiza_e_delete_remove_o_jogo(): void
    {
        $jogo = Jogo::create(['titulo' => 'Jogo Antigo ' . uniqid(), 'preco' => 20]);
        $novoTitulo = 'Jogo Atualizado ' . uniqid();

        $this->putJson('/api/jogos/' . $jogo->id_jogo, $this->dadosJogo(['titulo' => $novoTitulo]))
            ->assertOk()
            ->assertJsonPath('data.titulo', $novoTitulo);

        $this->deleteJson('/api/jogos/' . $jogo->id_jogo)->assertNoContent();

        $this->assertNull(Jogo::find($jogo->id_jogo));
    }
    public function test_get_horas_do_usuario_usa_a_function_do_banco(): void
    {
        $usuario = Usuario::create(['nome' => 'Teste Horas', 'email' => uniqid() . '@teste.dev', 'senha_hash' => 'x']);

        $this->getJson('/api/usuarios/' . $usuario->id_usuario . '/horas')
            ->assertOk()
            ->assertJsonStructure(['id_usuario', 'total_horas'])
            ->assertJsonPath('total_horas', 0);
    }