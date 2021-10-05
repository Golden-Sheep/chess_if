<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function getViewLogin(){

        if(Auth::check()){
            return redirect('home');
        }
        return view('auth.login');
    }

    public function getViewRegistro(){
        $campus = Campus::all();
        if(Auth::check()){
            return redirect('home');
        }
            return view('auth.register')->with(['campus' => $campus]);
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if(Auth::user()->nivel_de_acesso == 2) {
                return redirect()->intended('home');
            }
            return redirect()->intended('usuarios');
        }

        return redirect()
            ->back()
            ->withErrors(['login_message' => 'Email e/ou senha incorretos.'])
            ->withInput();
    }

    public function postLogout(Request $request)
    {
        Auth::logout();
        return redirect()->intended('/');
    }

    public function postRegistro(Request $request){

         $request->validate([
            'name'                          => 'required|min:2|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
            'sexo'                         => 'required|min:2|max:20|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'password'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'email'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
            'apelido'                          => 'required|min:3|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
             'prontuario'                         => 'required|min:5|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ,.-_]*$/',
         ]);

        if(Usuario::where('email', $request->email)->first()){
            return redirect()->back()->withInput()->withErrors(['email' => 'Esse email já está em uso.']);
        }



        $usuario = new Usuario;

        if($request->password != $request->password_confirmation){
            return redirect()->back()->withInput()->withErrors(['password' => 'Senhas não coincidem']);
        }

        if(!Usuario::validarSexo($request->sexo)){
            return redirect()->back()->withInput();
        }


            if (strlen($request->prontuario) > 11 || strlen($request->prontuario) < 5  ) {
                return redirect()->back()->withInput()->withErrors(['prontuario' => 'Prontuário não pode estar vazio ou ter mais que 11 digitos']);
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
        $usuario->nome = $request->name;
        $usuario->apelido = $request->apelido;
        $usuario->password = $request->password;
        $usuario->sexo = $request->sexo;
        $usuario->email = $request->email;
        $usuario->pontuacao = 300;
        $usuario->nivel_de_acesso = 2;

        if($usuario->save()){
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                return redirect()->intended('home');
            }
                return redirect('/');

        }else{
            return redirect()->back()->withInput()->withErrors(['register_message' => 'Algo deu errado, tente novamente!']);
        }

    }



}
