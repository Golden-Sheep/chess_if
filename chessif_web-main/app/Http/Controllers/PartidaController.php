<?php

namespace App\Http\Controllers;


use App\Models\Aviso;
use App\Models\HistoricosDeMovimento;
use App\Models\ListaTitulo;
use App\Models\Partida;
use App\Models\Titulo;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class PartidaController extends Controller
{
    //Parametro token de um usuário
    //Metodo feito para API
    public function verificarSeExistePartidaEmAndamento(Request $request){
        $token = $request->token;
        //SE TOKEN NÃO ESTIVER VAZIO
        if(!empty($token)) {
            $usuario = Usuario::where('token', $token)->first();
            //se encontrar o usuário
            if($usuario){
                //Aqui ele verifica se existe uma partida em andamento (onde o fim do jogo ainda não definido);
                if(Jogo::where('jogador_1', $usuario->id)->whereNull('fim_do_jogo')->count() > 0){
                    return response()->json([
                        'msg' => 'jogador em partida'
                    ], 302);
                }else{
                    return response()->json([
                        'msg' => 'jogador disponivel'
                    ], 200);
                }
            }else{
                return response()->json([
                    'message:' => 'Token invalido, nenhum usuário encontrado'
                ], 404);
            }
        }else{
            return response()->json([
                'msg' => 'Token não encontrado'
            ], 406);
        }
}


public function criarPartida(Request $resquest) {
        //Se não vier todos os dados ele retorna erro
        if(empty($resquest->pecaBranca) || empty($resquest->pecaPreta) ||empty($resquest->secret) || empty($resquest->modo)){
            return response()->json([
                'msg' => 'Falta de dados'
            ], 406);
        }else{
            //Se o segredo for invalido ele retorna erro
            if($resquest->secret != 'b4t4t4'){
                return response()->json([
                    'msg' => 'Não pode né moises'
                ], 404);
            }else{
                $jogador1 = $usuario = Usuario::find($resquest->pecaBranca);
                $jogador2 = $usuario = Usuario::find($resquest->pecaPreta);
                $partida = new Partida;
                $partida->jogador_1 = $jogador1->id;
                $partida->jogador_2 = $jogador2->id;
                $partida->pontuacao_jogador_1 = $jogador1->pontuacao;
                $partida->pontuacao_jogador_2 = $jogador2->pontuacao;
                $partida->modo = $resquest->modo;
                $partida->tempo_jogador_1 = '00:05:00';
                $partida->tempo_jogador_2 = '00:05:00';
                if($partida->save()){
                    return response()->json([
                        'msg' => 'Partida criada',
                        'idPartida' => $partida->id,
                    ], 200);
                }else{
                    return response()->json([
                        'msg' => 'Não foi possivel criar a partida'
                    ], 503);
                }
            }
        }
}


public function validarPartidaUsuario(Request $request){

        if(empty($request->token) || empty($request->idSala) ){
            return response()->json([
                'msg' => 'Falta de dados'
            ], 406);
        }else{

            $usuario = Usuario::where('token', $request->token)->first();

            if($request->pecaBranca){
                $autenticar = Jogo::where('id', $request->idSala)->where('jogador_1', $usuario->id)->first();
                if($autenticar){
                    return response()->json([
                        'msg' => 'FOI',
                        'pecaBranca' => 1
                    ], 200);

                }else{
                    //Error
                }
            }else{
                $autenticar = Jogo::where('id', $request->idSala)->where('jogador_2', $usuario->id)->first();
                if($autenticar) {
                    return response()->json([
                        'msg' => 'FOI',
                        'pecaBranca' => 0
                    ], 200);
                }else{
                    //error
                }

            }

        }
}

public function finalizarPartida(Request $request){

        if($request->secret && $request->partida){
            if($request->secret == 'b4t4t4') {

                $partida = Partida::find($request->partida['sala']);


                if($partida){
                    $jogador1 = Usuario::find($partida->jogador_1);
                    $jogador2 = Usuario::find($partida->jogador_2);
                    $partida->tempo_jogador_1 = $request->partida['tempoB'];
                    $partida->tempo_jogador_2 = $request->partida['tempoP'];
                    $partida->motivo = $request->partida['status'];

                    if($request->partida['ganhador']) {
                        if ($request->partida['ganhador'] == 'w') {
                            $partida->ganhador = $partida->jogador_1;
                            $partida->perdedor = $partida->jogador_2;
                            if($partida->modo == 'ranqueado') {
                                $partida->pontos_ganho_vencedor = abs($jogador1->pontuacao - $this->getRatingJogador($jogador1, $jogador2, 'vitoria'));
                                $partida->pontos_perdidos_perdedor = abs($jogador2->pontuacao - $this->getRatingJogador($jogador2, $jogador1, 'derrota'));
                                $jogador1->pontuacao = $this->getRatingJogador($jogador1, $jogador2, 'vitoria');
                                $jogador2->pontuacao = $this->getRatingJogador($jogador2, $jogador1, 'derrota');
                                if($jogador1->pontuacao < 1){
                                    $jogador1->pontuacao = 1;
                                }
                                if($jogador2->pontuacao < 1){
                                    $jogador2->pontuacao = 1;
                                }
                            }
                        } else {
                            $partida->ganhador = $partida->jogador_2;
                            $partida->perdedor = $partida->jogador_1;
                            if($partida->modo == 'ranqueado') {
                                $partida->pontos_ganho_vencedor = abs($jogador2->pontuacao - $this->getRatingJogador($jogador2, $jogador1, 'vitoria'));
                                $partida->pontos_perdidos_perdedor = abs($jogador1->pontuacao - $this->getRatingJogador($jogador1, $jogador2, 'derrota'));
                                $jogador1->pontuacao = $this->getRatingJogador($jogador2, $jogador1, 'vitoria');
                                $jogador2->pontuacao = $this->getRatingJogador($jogador1, $jogador2, 'derrota');
                                if($jogador1->pontuacao < 1){
                                    $jogador1->pontuacao = 1;
                                }
                                if($jogador2->pontuacao < 1){
                                    $jogador2->pontuacao = 1;
                                }
                            }
                        }
                    }else{
                        $partida->ganhador = null;
                        $partida->perdedor = null;
                        if($partida->modo == 'ranqueado') {
                            $jogador1->pontuacao = $this->getRatingJogador($jogador2, $jogador1, 'empate');
                            $jogador2->pontuacao = $this->getRatingJogador($jogador1, $jogador2, 'empate');
                            $partida->pontos_ganho_vencedor = abs($jogador1->pontuacao - $this->getRatingJogador($jogador1, $jogador2, 'empate'));
                            $partida->pontos_perdidos_perdedor =  abs($jogador2->pontuacao - $this->getRatingJogador($jogador2, $jogador1, 'empate'));
                            if($jogador1->pontuacao < 1){
                                $jogador1->pontuacao = 1;
                            }
                            if($jogador2->pontuacao < 1){
                                $jogador2->pontuacao = 1;
                            }
                        }
                    }

                    $partida->fim_do_jogo = date('Y-m-d H:i:s');
                    $partida->save();
                    $jogador1->save();
                    $jogador2->save();
                    $this->atribuirTitulos($jogador1);
                    $this->atribuirTitulos($jogador2);

                    if(sizeof($request->partida['partida']['movimento']) > 0){
                        for($i = 0; $i < sizeof($request->partida['partida']['movimento']);$i++){
                            $historico = new HistoricosDeMovimento;
                            $historico->id_partida = $partida->id;
                            $historico->n_movimento = $request->partida['partida']['movimento'][$i]['n_turno'];
                            $historico->source = $request->partida['partida']['movimento'][$i]['source'];
                            $historico->target = $request->partida['partida']['movimento'][$i]['target'];
                            $historico->fen = $request->partida['partida']['movimento'][$i]['fen'];
                            $historico->save();
                        }
                    }

                    return response()->json([
                        'msg' => 'Partida finalizada'
                    ], 200);
                }
                return response()->json([
                    'msg' => 'Partida não encontrada'
                ], 404);
            }
            return response()->json([
                'msg' => 'Não pode né moises'
            ], 404);
        }
    return response()->json([
        'msg' => 'Falta de dados'
    ], 404);
}


    public function getRatingJogador($jogadorPrincipal, $jogadorSecundario, $status){
        $json =  $this->jsonScore();

        $jogador1 = $jogadorPrincipal;
        $jogador2 = $jogadorSecundario;

        $diferenca =  round(abs($jogador2->pontuacao - $jogador1->pontuacao));
        $regra = null;
        foreach ($json as $item){
            if($diferenca >= $item['minimo'] && $diferenca <=$item['maximo']){
                $regra = $item;
                break;
            }
        }
        if(!$regra){
            $regra = $json[50];
        }


        if($jogador1->pontuacao > $jogador2->pontuacao){
            $portentagem = $regra['sup'];
        }else{
            $portentagem = $regra['inf'];
        }

        if($status == 'vitoria') {
            //vitoria
            $po = 1;
            $pe = $portentagem / 100;
            $calculo = 10 * ($po - $pe);

            $pontosGanho = round($jogador1->pontuacao + $calculo);
            return $pontosGanho;
        }

        if($status == 'empate') {
            $po = 0.5;
            $pe = $portentagem / 100;
            $calculo = 10 * ($po - $pe);

            $pontosGanho = round($jogador1->pontuacao + $calculo);
            return $pontosGanho;
        }

        if($status == 'derrota') {
            $po = 0.0;
            $pe = $portentagem / 100;
            $calculo = 10 * ($po - $pe);

            $pontosGanho = round($jogador1->pontuacao + $calculo);
            return $pontosGanho;
        }

    }


    public function jsonScore(){
        return json_decode('[{"minimo":"0","maximo":"3","sup":50,"inf":50},{"minimo":"4","maximo":"10","sup":51,"inf":49},{"minimo":"11","maximo":"17","sup":52,"inf":48},{"minimo":"18","maximo":"25","sup":53,"inf":47},{"minimo":"26","maximo":"32","sup":54,"inf":46},{"minimo":"33","maximo":"39","sup":55,"inf":45},{"minimo":"40","maximo":"46","sup":56,"inf":44},{"minimo":"47","maximo":"53","sup":57,"inf":43},{"minimo":"54","maximo":"61","sup":58,"inf":42},{"minimo":"62","maximo":"68","sup":59,"inf":41},{"minimo":"69","maximo":"76","sup":60,"inf":40},{"minimo":"77","maximo":"83","sup":61,"inf":39},{"minimo":"84","maximo":"91","sup":62,"inf":38},{"minimo":"92","maximo":"98","sup":63,"inf":37},{"minimo":"99","maximo":"106","sup":64,"inf":36},{"minimo":"107","maximo":"113","sup":65,"inf":35},{"minimo":"114","maximo":"121","sup":66,"inf":34},{"minimo":"122","maximo":"129","sup":67,"inf":33},{"minimo":"130","maximo":"137","sup":68,"inf":32},{"minimo":"138","maximo":"145","sup":69,"inf":31},{"minimo":"146","maximo":"153","sup":70,"inf":30},{"minimo":"154","maximo":"162","sup":71,"inf":29},{"minimo":"163","maximo":"170","sup":72,"inf":28},{"minimo":"171","maximo":"179","sup":73,"inf":27},{"minimo":"180","maximo":"188","sup":74,"inf":26},{"minimo":"189","maximo":"197","sup":75,"inf":25},{"minimo":"198","maximo":"206","sup":76,"inf":24},{"minimo":"207","maximo":"215","sup":77,"inf":23},{"minimo":"216","maximo":"225","sup":78,"inf":22},{"minimo":"226","maximo":"235","sup":79,"inf":21},{"minimo":"236","maximo":"245","sup":80,"inf":20},{"minimo":"246","maximo":"256","sup":81,"inf":19},{"minimo":"257","maximo":"267","sup":82,"inf":18},{"minimo":"268","maximo":"278","sup":83,"inf":17},{"minimo":"279","maximo":"290","sup":84,"inf":16},{"minimo":"291","maximo":"302","sup":85,"inf":15},{"minimo":"303","maximo":"315","sup":86,"inf":14},{"minimo":"316","maximo":"328","sup":87,"inf":13},{"minimo":"329","maximo":"344","sup":88,"inf":12},{"minimo":"345","maximo":"357","sup":89,"inf":11},{"minimo":"358","maximo":"374","sup":90,"inf":10},{"minimo":"375","maximo":"391","sup":91,"inf":9},{"minimo":"392","maximo":"411","sup":92,"inf":8},{"minimo":"412","maximo":"432","sup":93,"inf":7},{"minimo":"433","maximo":"456","sup":94,"inf":6},{"minimo":"457","maximo":"484","sup":95,"inf":5},{"minimo":"485","maximo":"517","sup":96,"inf":4},{"minimo":"518","maximo":"559","sup":97,"inf":3},{"minimo":"560","maximo":"619","sup":98,"inf":2},{"minimo":"620","maximo":"735","sup":99,"inf":1},{"minimo":"735","maximo":"735","sup":100,"inf":0}]', true);
    }

    public function atribuirTitulos($usuario){
        $listaTitulos =  $usuario->lista_titulos->pluck('titulo')->toArray();
        if($listaTitulos > 0){
            $titulos = Titulo::whereNotIn('id', $listaTitulos)->where('qtd_partida_necessaria','>', 0)->get();
            $qtdPartidasUsuario =  $usuario->partidas->count();
            foreach ($titulos as $titulo){
                if($titulo->qtd_partida_necessaria <= $qtdPartidasUsuario){
                    $lista = new ListaTitulo;
                    $lista->usuario = $usuario->id;
                    $lista->titulo = $titulo->id;
                    $lista->save();

                    $aviso = new Aviso;
                    $aviso->de = null;
                    $aviso->para = $usuario->id;
                    $aviso->texto = 'Parabéns um novo título foi atribuído a sua conta!'.' -> '.$titulo->nome;
                    $aviso->sistema = 1;
                    $aviso->save();
                }
            }
        }
    }

}
