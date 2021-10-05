<?php

namespace App\Http\Controllers;

use App\Exports\UsuariosTaxaDeVitoriaExport;
use App\Models\Partida;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class RelatorioController extends Controller
{

    public function calcTaxaVitoria($dataInicial, $dataFinal, $idJogador){
        $partidas = Partida::where('created_at', '>=', $dataInicial.' 00:00:00')->where('created_at', '<=', $dataFinal .' 23:59:99')->where('jogador_1', $idJogador)->orWhere('jogador_2',$idJogador)->get();
        $vitorias = 0;
        foreach ($partidas as $partida){
            if($partida->ganhador == $idJogador){
                $vitorias++;
            }
        }

        $jogador = Usuario::find($idJogador);

        return ['jogador' => $jogador, 'qtdPartida' => $partidas->count(), 'vitorias' => $vitorias ];

    }

    public function getViewRelatorioWinRate(){
       return view('relatorio.winrate.index');
    }

    public function getGerarPdfWinrate($dataInicial, $dataFinal){
        $collect = collect();
        $patidas = Partida::where('created_at', '>=', $dataInicial.' 00:00:00')->where('created_at', '<=', $dataFinal .' 23:59:99')->get();
        if($patidas->count() > 0){
            foreach ($patidas as $partida) {
                $encontrei = false;
                foreach ($collect as $item) {
                    if ($item['jogador'] == $partida->jogador_1) {
                        $encontrei = true;
                    }
                }
                if (!$encontrei) {
                    $collect->push(['jogador' => $partida->jogador_1]);
                }
                $encontrei = false;

                foreach ($collect as $item) {
                    if ($item['jogador'] == $partida->jogador_2) {
                        $encontrei = true;
                    }
                }
                if (!$encontrei) {
                    $collect->push(['jogador' => $partida->jogador_2]);
                }
            }
            $jogadores = collect();
            foreach ($collect as $item) {
                $jogadores->push($this->calcTaxaVitoria($dataInicial, $dataFinal, $item['jogador']));
            }


            $pdf = PDF::loadView('relatorio.winrate.template-pdf', [
                'titulo' => 'Taxa de vitória dos jogadores',
                'datainicio' => $dataInicial,
                'datafim' => $dataFinal,
                'jogadores' => $jogadores
            ]);

            $pdf->setOption('encoding', 'utf-8');
            $pdf->setOption('header-spacing', 2);
            $pdf->setOption('footer-spacing', 2);
            $pdf->setOption('header-font-name', 'sans-serif');
            $pdf->setOption('header-font-size', 8);
            $pdf->setOption('footer-spacing', 2);
            $pdf->setOption('footer-font-name', 'sans-serif');
            $pdf->setOption('footer-font-size', 8);
            $pdf->setOption('footer-left', 'ChessIF | IFSP');
            $pdf->setOption('footer-center', 'Impresso por ' . Auth::user()->nome . ', em ' . date('d/m/Y H:i:s', time()));
            $pdf->setOption('footer-right', '[page]/[toPage]');

            $dt = date("YmdHis");
            return $pdf->inline('taxa-de-vitoria-' . $dt . '.pdf');
        }else{

        }
    }

    public function getGerarXlsxWinrate($dataInicial, $dataFinal){

        $collect = collect();
        $patidas = Partida::where('created_at', '>=', $dataInicial.' 00:00:00')->where('created_at', '<=', $dataFinal .' 23:59:99')->get();
        if($patidas->count() > 0) {
            foreach ($patidas as $partida) {
                $encontrei = false;
                foreach ($collect as $item) {
                    if ($item['jogador'] == $partida->jogador_1) {
                        $encontrei = true;
                    }
                }
                if (!$encontrei) {
                    $collect->push(['jogador' => $partida->jogador_1]);
                }
                $encontrei = false;

                foreach ($collect as $item) {
                    if ($item['jogador'] == $partida->jogador_2) {
                        $encontrei = true;
                    }
                }
                if (!$encontrei) {
                    $collect->push(['jogador' => $partida->jogador_2]);
                }
            }

            $jogadores = collect();
            foreach ($collect as $item) {
                $jogadores->push($this->calcTaxaVitoria($dataInicial, $dataFinal, $item['jogador']));
            }

            $arrayTratado = collect();

            foreach ($jogadores as $jogador) {
                $arrayTratado->push(
                    [
                        'jogador' => $jogador['jogador']['nome'] . ' [' . $jogador['jogador']['apelido'] . ']',
                        'qtdPartida' => $jogador['qtdPartida'],
                        'qtdVitoria' => $jogador['vitorias'],
                        'tavaVitoria' => round($jogador['vitorias'] / $jogador['qtdPartida'] * 100)
                    ]
                );
            }

            return Excel::download(new UsuariosTaxaDeVitoriaExport($arrayTratado), 'relatorio-taxa-de-vitoria.xlsx');
        }else{
            return redirect('/relatorio/jogadores/winrate')->withErrors(['error' => 'Nenhum partida contabilizada nesse periodo de tempo']);
        }
    }


    public function getViewRelatorioAtividadeJogadores(){
        return view('relatorio.atividade-jogadores.index');
    }

    public function getGerarPdfAtividadeJogadores($dataInicial, $dataFinal){
        $usuarioNovos = Usuario::select('id', 'nome', 'apelido')->where('created_at', '>=', $dataInicial.' 00:00:00')->where('created_at', '<=', $dataFinal .' 23:59:99')->get();
        $collect = collect();
        if($usuarioNovos->count() > 0){
            foreach ($usuarioNovos as $usuario){
              $vitoria = 0;
              $partidas = Partida::where('jogador_1', $usuario->id)->orWhere('jogador_2', $usuario->id)->get();

              foreach ($partidas as $partida){
                  if($partida->ganhador == $usuario->id){
                      $vitoria ++;
                  }
              }
              $winRate = null;
              if($partidas->count() > 0) {
                  $winRate = round(($vitoria / $partidas->count()) * 100);
              }
              $collect->push([
                  'usuario' => $usuario,
                  'qtdPartidas' => $partidas->count(),
                  'partidas' => $partidas,
                  'vitoria' => $vitoria,
                  'winrate' => $winRate,
              ]);

            }

            $pdf = PDF::loadView('relatorio.atividade-jogadores.template-pdf', [
                'titulo' => 'Atividades de novos jogadores',
                'datainicio' => $dataInicial,
                'datafim' => $dataFinal,
                'jogadores' => $collect
            ]);

            $pdf->setOption('encoding', 'utf-8');
            $pdf->setOption('header-spacing', 2);
            $pdf->setOption('footer-spacing', 2);
            $pdf->setOption('header-font-name', 'sans-serif');
            $pdf->setOption('header-font-size', 8);
            $pdf->setOption('footer-spacing', 2);
            $pdf->setOption('footer-font-name', 'sans-serif');
            $pdf->setOption('footer-font-size', 8);
            $pdf->setOption('footer-left', 'ChessIF | IFSP');
            $pdf->setOption('footer-center', 'Impresso por ' . Auth::user()->nome . ', em ' . date('d/m/Y H:i:s', time()));
            $pdf->setOption('footer-right', '[page]/[toPage]');

            $dt = date("YmdHis");
            return $pdf->inline('atividade-novos-jogoadores-' . $dt . '.pdf');
        }

    }


}
