<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ListaTitulo;
use App\Models\Titulo;
use App\Models\Usuario;
use Illuminate\Http\Request;

class TitulosController extends Controller
{
    public function getViewTitulos(){
        $titulos = Titulo::all();
        return view('titulo.index')->with(['titulos' => $titulos]);
    }

    public function getViewCadastro(){
        return view('titulo.cadastro');
    }

    public function postCadastrar(Request $request){
        $request->validate([
            'nome'                          => 'required|min:3|max:200|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
            'descricao'                         => 'required|min:5|max:300|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
        ]);


        $titulo = new Titulo;
        $titulo->nome = $request->nome;
        $titulo->descricao = $request->descricao;
        if($request->metrica){
            $numero = $request->qtd_partidas;
            if($numero < 1){
                $numero = 1;
            }
            $titulo->qtd_partida_necessaria = $numero;
        }

        $titulo->save();

        return redirect('/titulos');
    }

    public function getViewEditar($id){
        $titulo = Titulo::find($id);
        if($titulo){
            return view('titulo.editar')->with(['titulo' => $titulo]);
        }
        return redirect('/titulos')->withInput()->withErrors(['error' => 'Título não encontrado!']);
    }

    public function postEditar(Request $request){
        $titulo = Titulo::find($request->id);
        if($titulo){
            $request->validate([
                'nome'                          => 'required|min:3|max:200|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
                'descricao'                         => 'required|min:5|max:300|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            ]);
            $titulo->nome = $request->nome;
            $titulo->descricao = $request->descricao;
            if($request->metrica){
                $numero = $request->qtd_partidas;
                if($numero < 1){
                    $numero = 1;
                }
                $titulo->qtd_partida_necessaria = $numero;
            }else{
                $titulo->qtd_partida_necessaria = 0;
            }
            $titulo->save();
            return redirect('/titulos');
        }
        return redirect('/titulos')->withInput()->withErrors(['error' => 'Título não encontrado!']);
    }


    public function getViewGerenciarTitulo(){
        $usuario = Usuario::where('nivel_de_acesso', 2)->where('banido', 0)->orWhere('banido', null)->get();
        return view('titulo.gerenciar.index')->with(['usuario' => $usuario]);
    }

    public function getViewTitulosJogador($id){
        $usuario = Usuario::find($id);
        if($usuario){
            $titulos = Titulo::all();
            return view('titulo.gerenciar.lista-de-titulo')->with(['titulos' => $titulos, 'usuario' => $usuario]);
        }
        return redirect('gerenciartitulo')->withErrors(['error' => 'Usuário não encontrado!']);
    }

    public function postAdicionarTituloJogador(Request $request){
        if(Usuario::find($request->jogador) && Titulo::find($request->titulo)){

            if(!(ListaTitulo::where('usuario',$request->jogador)->where('titulo', $request->titulo)->count())){
                $lista = new ListaTitulo;
                $lista->usuario = $request->jogador;
                $lista->titulo = $request->titulo;
                $lista->save();
                return response()->json([
                    'msg' => 'Algo deu errado',
                ], 200);
            }

            return response()->json([
                'msg' => 'Algo deu errado',
            ], 202);
        }

        return response()->json([
            'msg' => 'Usuário ou Titulo não encontrado',
        ], 2002);
    }


    public function postRemoverTituloJogador(Request $request){
        if(Usuario::find($request->jogador) && Titulo::find($request->titulo)){

            if(ListaTitulo::where('usuario',$request->jogador)->where('titulo', $request->titulo)->count()){
                $lista = ListaTitulo::where('usuario',$request->jogador)->where('titulo', $request->titulo)->first();
                $lista->delete();
                return response()->json([
                    'msg' => 'Titulo removido',
                ], 200);
            }

            return response()->json([
                'msg' => 'Algo deu errado',
            ], 202);

        }

        return response()->json([
            'msg' => 'Usuário ou Titulo não encontrado',
        ], 202);
    }

    public function postExcluir(Request $request){
        $titulo = Titulo::find($request->id);
        if($titulo){
            if($titulo->lista_titulos->count() > 0){
                return redirect('/titulos')->withInput()->withErrors(['error' => 'Usuários utilizando esse título!']);
            }
            $titulo->delete();
            return redirect('/titulos');
        }
        return redirect('/titulos')->withInput()->withErrors(['error' => 'Título não encontrado!']);
    }


}
