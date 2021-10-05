<?php

namespace App\Http\Controllers;


use App\Models\Partida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JogoController extends Controller
{
   public function getViewJogo($id){
       $partida = Partida::find($id);
       $usuario = Auth::user();

       if($partida) {
           if($partida->fim_do_jogo){
               $msg = 'Partida finalizada. | Ganhador: '
                   .$partida->Ganhador->apelido.' | Motivo da vitoria: '.$partida->motivo;
               return redirect('/home')->withErrors(['error' => $msg]);

           }
           return view('jogo.tabuleiro')->with(['partida' => $partida, 'usuario' => $usuario]);
       }else{
            return redirect('/');
       }

   }


   public function getRedirecionarModo($id){
       $partida = Partida::find($id);
       $usuario = Auth::user();
       if($partida) {
           if($partida->fim_do_jogo){
               $msg = 'Partida finalizada. | Ganhador: '
                   .$partida->Ganhador->apelido.' | Motivo da vitoria: '.$partida->motivo;
               return redirect('/home')->withErrors(['error' => $msg]);

           }
           if($partida->modo == 'casual'){
            return redirect('partida/casual/'.$partida->id);
           }else{
            return redirect('partida/ranqueada/'.$partida->id);
           }
       }else{
           return redirect('/');
       }
   }


   public function getViewReplay($id){
       $partida = Partida::find($id);

       if($partida) {
           if($partida->fim_do_jogo){
               if($partida->historicos_de_movimentos->count() > 0 ) {
                   $historico = [];
                   foreach ($partida->historicos_de_movimentos as $item){
                       $historico[] = [
                           'source' => $item->source,
                           'target' => $item->target
                       ];
                   }
                   return view('jogo.replay')->with(['partida' => $partida, 'historico' => json_encode($historico), 'historicoObj' => $partida->historicos_de_movimentos]);
               }else{
                   $msg = 'Partida sem histórico de movimentos';
                   return redirect()->back()->withErrors(['error' => $msg]);
               }
           }else{
               $msg = 'Partida em andamento';
               return redirect()->back()->withErrors(['error' => $msg]);
           }
       }else{
           $msg = 'Partida não encontrada';
           return redirect()->back()->withErrors(['error' => $msg]);
       }
   }

}
