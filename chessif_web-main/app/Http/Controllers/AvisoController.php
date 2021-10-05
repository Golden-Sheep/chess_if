<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvisoController extends Controller
{
   public function getViewAviso(){
        $aviso = Aviso::where('sistema', 0)->get()->groupBy('texto');
       return view('aviso.index')->with(['aviso' => $aviso]);
   }

   public function getViewCadastro(){
       return view('aviso.cadastro');
   }

    public function postCadastrar(Request $request){
        $request->validate([
            'descricao'                          => 'required|min:10|max:3000',
        ]);

        $usuario = Usuario::all();
        foreach ($usuario as $item){
            $aviso = new Aviso;
            $aviso->de = Auth::user()->id;
            $aviso->para = $item->id;
            $aviso->texto = $request->descricao;
            $aviso->sistema = 0;
            $aviso->save();
        }

        return redirect('/avisos');
    }

    public function getViewMeusAvisos(){
        $aviso = Auth::user()->aviso->where('para', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        Aviso::where('para', Auth::user()->id)->update(['visualizada' => 1]);
       return view('aviso.meus-avisos')->with(['aviso' => $aviso]);
    }

}
