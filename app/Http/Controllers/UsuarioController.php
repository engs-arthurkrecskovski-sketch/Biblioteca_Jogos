<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\LogBiblioteca;
use App\Models\Usuario;
use App\Services\BibliotecaService;
use App\Services\UsuarioService;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function __construct(
        private UsuarioService $service,
        private BibliotecaService $bibliotecaService
    ) {
    }

    public function index(Request $request)
    {
        $busca = $request->query('busca');

        $usuarios = Usuario::query()
            ->when($busca, function ($q) use ($busca) {
                $q->where(function ($q2) use ($busca) {
                    $q2->where('nome', 'ilike', "%{$busca}%")
                        ->orWhere('email', 'ilike', "%{$busca}%");
                });
            })
            ->orderBy('id_usuario')
            ->paginate(15)
            ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create', ['usuario' => new Usuario()]);
    }

    public function store(UsuarioRequest $request)
    {
        $usuario = $this->service->criar($request->validated());

        return redirect()->route('usuarios.show', $usuario)
            ->with('sucesso', 'Usuário cadastrado com sucesso.');
    }

    public function show(Usuario $usuario)
    {
        $usuario->load('assinatura');

        $biblioteca = $usuario->biblioteca()
            ->with('jogo')
            ->orderByDesc('horas_jogadas')
            ->limit(20)
            ->get();

        $logs = LogBiblioteca::where('id_usuario', $usuario->id_usuario)
            ->orderByDesc('id_log')
            ->limit(5)
            ->get();

        $totalHoras = $this->bibliotecaService->totalHoras($usuario->id_usuario);

        return view('usuarios.show', compact('usuario', 'biblioteca', 'logs', 'totalHoras'));
    }

    public function edit(Usuario $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(UsuarioRequest $request, Usuario $usuario)
    {
        $this->service->atualizar($usuario, $request->validated());

        return redirect()->route('usuarios.show', $usuario)
            ->with('sucesso', 'Usuário atualizado com sucesso.');
    }

    public function destroy(Usuario $usuario)
    {
        $this->service->excluir($usuario);

        return redirect()->route('usuarios.index')
            ->with('sucesso', 'Usuário excluído com sucesso.');
    }
}
