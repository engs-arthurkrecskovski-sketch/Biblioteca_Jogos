<?php

namespace Tests\Feature;

use App\Models\Desenvolvedora;
use App\Models\Genero;
use App\Models\Jogo;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Testes das rotas WEB (Blade).
 * DatabaseTransactions: tudo que o teste grava e desfeito no final (rollback),
 * entao os dados reais do banco nao sao alterados.
 */
class JogoWebTest extends TestCase
{
    use DatabaseTransactions;

    // WEB 1: a listagem de jogos abre e mostra um jogo cadastrado
    public function test_listagem_de_jogos_exibe_os_dados_do_banco(): void
    {
        $titulo = 'Jogo Web Listagem ' . uniqid();
        Jogo::create(['titulo' => $titulo, 'preco' => 19.90]);

        $resposta = $this->get('/jogos?busca=' . urlencode($titulo));

        $resposta->assertOk();
        $resposta->assertSee('Jogos');
        $resposta->assertSee($titulo);
    }

    // WEB 2: o formulario de cadastro grava um jogo e redireciona
    public function test_cadastro_de_jogo_pelo_formulario_grava_no_banco(): void
    {
        $titulo = 'Jogo Web Cadastro ' . uniqid();

        $resposta = $this->post('/jogos', [
            'titulo' => $titulo,
            'descricao' => 'Criado pelo teste automatizado',
            'preco' => 59.90,
            'data_lancamento' => '2026-03-10',
            'id_genero' => Genero::query()->value('id_genero'),
            'id_desenvolvedora' => Desenvolvedora::query()->value('id_desenvolvedora'),
        ]);

        $resposta->assertRedirect();
        $this->assertTrue(Jogo::where('titulo', $titulo)->exists());
    }

    // WEB 3: validacao do formulario (titulo e preco sao obrigatorios)
    public function test_cadastro_sem_titulo_retorna_erros_de_validacao(): void
    {
        $resposta = $this->post('/jogos', ['titulo' => '', 'preco' => '']);

        $resposta->assertSessionHasErrors(['titulo', 'preco']);
    }

    // WEB 4 (integracao com o TRIGGER): avaliar um jogo recalcula a nota no banco
    public function test_avaliacao_pela_tela_atualiza_nota_do_jogo_via_trigger(): void
    {
        $jogo = Jogo::create(['titulo' => 'Jogo Trigger ' . uniqid(), 'preco' => 10]);
        $usuario1 = Usuario::create(['nome' => 'Teste 1', 'email' => uniqid() . '@teste.dev', 'senha_hash' => 'x']);
        $usuario2 = Usuario::create(['nome' => 'Teste 2', 'email' => uniqid() . '@teste.dev', 'senha_hash' => 'x']);

        $this->post('/avaliacoes', ['id_usuario' => $usuario1->id_usuario, 'id_jogo' => $jogo->id_jogo, 'nota' => 5])
            ->assertRedirect();
        $this->post('/avaliacoes', ['id_usuario' => $usuario2->id_usuario, 'id_jogo' => $jogo->id_jogo, 'nota' => 3])
            ->assertRedirect();

        $jogo = $jogo->fresh();

        // media de 5 e 3 = 4.00, calculada pelo trigger trg_atualiza_nota_jogo
        $this->assertSame(2, $jogo->total_avaliacoes);
        $this->assertEquals(4.00, $jogo->nota_media);
    }
}
