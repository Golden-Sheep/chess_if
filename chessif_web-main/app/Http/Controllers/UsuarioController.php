<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ListaTitulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{


    // ====== Rotas API ====== //
    public function validarTokenUsuario(Request $request){

        if(empty($request->token)){
            return response()->json([
                'msg' => 'Token não encontrado'
            ], 406);
        }

        $usuario = Usuario::where('token', $request->token)->first();

        if($usuario){
            return response()->json([
                'msg' => 'Token valido, usuário encontrado',
                'pontuacao' => $usuario->pontuacao
            ], 202);
        }else{
            return response()->json([
                'msg' => 'Token invalido, nenhum usuário encontrado'
            ], 404);
        }

    }


    public function getViewUsuarios(){
        $usuario = Usuario::where('banido', 0)->orWhere('banido', null)->get();
        return view('usuario.index')->with(['usuario' => $usuario]);
    }

    public function getViewCadastro(){
        $campus = Campus::all();
      return view('usuario.cadastro')->with(['campus' => $campus]);
    }

    public function postCadastrarUsuario(Request $request){

        $request->validate([
            'nome'                          => 'required|min:2|max:200|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
            'sexo'                         => 'required|min:2|max:20|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'password'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'email'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'prontuario'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'apelido'                         => 'required|min:4|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
        ]);

        if(Usuario::where('email', $request->email)->first()){
            return redirect()->back()->withInput()->withErrors(['email' => 'Esse email já está em uso.']);
        }

        if(Usuario::where('prontuario', $request->prontuario)->first()){
            return redirect()->back()->withInput()->withErrors(['prontuario' => 'Esse prontuário já está em uso.']);
        }

        if(Usuario::where('apelido', $request->apelido)->first()){
            return redirect()->back()->withInput()->withErrors(['apelido' => 'Esse apelido já está em uso.']);
        }

        if(!Usuario::validarSexo($request->sexo)){
            return redirect()->back()->withInput();
        }

        if ($request->campus) {
            if (!Campus::find($request->campus)) {
                return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
            }
        }else{
            return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
        }

        $usuario = new Usuario;
        $usuario->campus = $request->campus;
        $usuario->prontuario = $request->prontuario;
        $usuario->nome = $request->nome;
        $usuario->password = $request->password;
        $usuario->sexo = $request->sexo;
        $usuario->email = $request->email;
        $usuario->apelido = $request->apelido;
        $usuario->pontuacao = 300;
        $usuario->nivel_de_acesso = $request->nivel_de_acesso;
        $usuario->save();

        return redirect('/usuarios');

    }

    public function getViewEditar($id){
        $usuario = Usuario::find($id);
        $btnVoltar = redirect()->back()->getTargetUrl();
        if($usuario){
            $campus = Campus::all();
            return view('usuario.editar')->with(['usuario' => $usuario, 'campus' => $campus, 'btnVoltar' =>
                $btnVoltar]);
        }
        return redirect('/usuarios')->withInput()->withErrors(['error' => 'Usuário não encontrado!']);

    }


    public function postEditar(Request $request){
        $usuario = Usuario::find($request->id);
        if($usuario){
            $request->validate([
                'nome'                          => 'required|min:2|max:200|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
                'sexo'                         => 'required|min:2|max:20|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'password'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'email'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'prontuario'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'apelido'                         => 'required|min:4|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            ]);

            if($usuario->email != $request->email){
                if(Usuario::where('email', $request->email)->first()){
                    return redirect()->back()->withInput()->withErrors(['email' => 'O email digitado já está em uso.']);
                }
            }

            if($usuario->prontuario != $request->prontuario) {
                if (Usuario::where('prontuario', $request->prontuario)->first()) {
                    return redirect()->back()->withInput()->withErrors(['prontuario' => 'O prontuário digitado já está em uso.']);
                }
            }

            if($usuario->apelido != $request->apelido) {
                if (Usuario::where('apelido', $request->apelido)->first()) {
                    return redirect()->back()->withInput()->withErrors(['apelido' => 'O apelido digitado já está em uso.']);
                }
            }

            if(!Usuario::validarSexo($request->sexo)){
                return redirect()->back()->withInput();
            }

            if ($request->campus) {
                if (!Campus::find($request->campus)) {
                    return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
                }
            }else{
                return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
            }


            $usuario->campus = $request->campus;
            $usuario->prontuario = $request->prontuario;
            $usuario->nome = $request->nome;
            $usuario->password = $request->password;
            $usuario->sexo = $request->sexo;
            $usuario->email = $request->email;
            $usuario->apelido = $request->apelido;
            $usuario->nivel_de_acesso = $request->nivel_de_acesso;
            $usuario->save();

            return redirect('/usuarios');

        }
            return redirect('/usuarios')->withInput()->withErrors(['error' => 'Usuário não encontrado!']);
    }

    public function postBloquearUsuario(Request $request)
    {
        $usuario = Usuario::find($request->id);

        if ($usuario && $usuario->id != Auth::user()->id) {
            $usuario->banido  = 1;
            $usuario->save();
            return response()->json([
                'msg' => 'Usuário bloqueado!',
            ], 200);
        }

        return response()->json([
            'msg' => 'Usuário não encontrado',
        ], 202);


    }

    public function postDesbloquearUsuario(Request $request){
        $usuario = Usuario::find($request->id);

        if ($usuario) {
            $usuario->banido  = 0;
            $usuario->save();
            return response()->json([
                'msg' => 'Usuário bloqueado!',
            ], 200);
        }

        return response()->json([
            'msg' => 'Usuário não encontrado',
        ], 400);


    }

    public function getViewUsuariosBloqueados(){
        $usuario = Usuario::where('banido', 1)->get();
        return view('usuario.bloqueado')->with(['usuario' => $usuario]);
    }

    public function getViewRanking(){
        $usuario = Usuario::where('nivel_de_acesso',2)->where('banido', 0)->orWhere('banido', null)->orderBy('pontuacao', 'Desc')->get();
        return view('usuario/melhores-jogadores')->with(['usuario' => $usuario]);
    }

    public function getMeuPefil(){
        $btnVoltar = redirect()->back()->getTargetUrl();
        $campus = Campus::all();
        $partida = Auth::user()->partidas;
        $titulo = ListaTitulo::where('usuario',Auth::user()->id)->get();
        return view('perfil.meu-perfil')->with(['usuario' => Auth::user(), 'campus' => $campus, 'btnVoltar' =>
            $btnVoltar, 'partida' => $partida, 'titulo' => $titulo]);
    }

    public function postEditarPerfil(Request $request){
        $usuario = Usuario::find(Auth::user()->id);
        if($usuario){
            $request->validate([
                'nome'                          => 'required|min:2|max:200|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
                'sexo'                         => 'required|min:2|max:20|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'password'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'email'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'prontuario'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
                'apelido'                         => 'required|min:4|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            ]);

            if($usuario->email != $request->email){
                if(Usuario::where('email', $request->email)->first()){
                    return redirect()->back()->withInput()->withErrors(['email' => 'O email digitado já está em uso.']);
                }
            }

            if($usuario->prontuario != $request->prontuario) {
                if (Usuario::where('prontuario', $request->prontuario)->first()) {
                    return redirect()->back()->withInput()->withErrors(['prontuario' => 'O prontuário digitado já está em uso.']);
                }
            }

            if($usuario->apelido != $request->apelido) {
                if (Usuario::where('apelido', $request->apelido)->first()) {
                    return redirect()->back()->withInput()->withErrors(['apelido' => 'O apelido digitado já está em uso.']);
                }
            }

            if(!Usuario::validarSexo($request->sexo)){
                return redirect()->back()->withInput();
            }

            if ($request->campus) {
                if (!Campus::find($request->campus)) {
                    return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
                }
            }else{
                return redirect()->back()->withInput()->withErrors(['campus' => 'Campus não encontrado na nossa base de dados']);
            }


            $usuario->campus = $request->campus;
            $usuario->prontuario = $request->prontuario;
            $usuario->nome = $request->nome;
            $usuario->password = $request->password;
            $usuario->sexo = $request->sexo;
            $usuario->email = $request->email;
            $usuario->apelido = $request->apelido;
            $usuario->save();

            if($request->titulo){
                $titulo = ListaTitulo::where('usuario', $usuario->id)->where('titulo', $request->titulo)->first();
                if($titulo){
                    ListaTitulo::where('usuario', $usuario->id)->update(['ativo' => 0]);
                    $titulo->ativo = 1;
                    $titulo->save();
                }
            }

            return redirect('/');

        }
        return redirect()->back()->withInput()->withErrors(['error' => 'Usuário não encontrado!']);
    }

    public function getPerfil($id){
        $usuario = Usuario::find($id);
        if($usuario){
            $btnVoltar = redirect()->back()->getTargetUrl();
            $partida = $usuario->partidas;
            $todosUsuario = Usuario::select('id')->where('nivel_de_acesso',2)->where('banido', 0)->orWhere('banido',
                    null)->orderBy('pontuacao', 'Desc')->get();
            $posicao=1;
            foreach($todosUsuario as $item){
                if($usuario->id == $item->id){
                    break;
                }
                $posicao++;
            }

            return view('perfil.index')->with(['usuario' => $usuario, 'btnVoltar' =>
                $btnVoltar, 'partida' => $partida, 'p' => $posicao]);
        }
        return "f";

    }

}
