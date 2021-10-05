<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function getViewCampus(){
        $campus = Campus::all();
        return view('campus.index')->with(['campus' => $campus]);
    }


    public function getViewCadastro(){
        return view('campus.cadastro');
    }

    public function postCadastrar(Request $request){
        $request->validate([
            'sigla'                          => 'required|min:1|max:5|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
            'nome'                          => 'required|min:1|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
        ]);
        $campus = new Campus;
        $campus->sigla = $request->sigla;
        $campus->nome = $request->nome;
        $campus->save();
        return redirect('/campus');
    }

    public function getViewEditar($id){
        $campus = Campus::find($id);
        if($campus){
            return view('campus.editar')->with(['campus' => $campus]);
        }
        return redirect('/campus')->withInput()->withErrors(['error' => 'Campus não encontrado!']);
    }

    public function postEditar(Request $request){
        $campus = Campus::find($request->id);
        if($campus){
            $request->validate([
                'sigla'                          => 'required|min:1|max:5|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
                'nome'                          => 'required|min:1|max:100|regex:/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9 ]*$/',
            ]);
            $campus->sigla = $request->sigla;
            $campus->nome = $request->nome;
            $campus->save();
            return redirect('/campus');
        }
        return redirect('/campus')->withInput()->withErrors(['error' => 'Campus não encontrado!']);
    }

    public function postExcluir(Request $request){
        $campus = Campus::find($request->id);
        if($campus){
            if($campus->usuarios->count() > 0){
                return redirect('/campus')->withInput()->withErrors(['error' => 'Usuários utilizando esse campus!']);
            }
            $campus->delete();
            return redirect('/campus');
        }
        return redirect('/campus')->withInput()->withErrors(['error' => 'Campus não encontrado!']);
    }
}
