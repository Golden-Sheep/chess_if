@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <div class="row">

        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <b>{{$partida->Pecabranca->apelido}}</b><small> ({{$partida->Pecabranca->nome}})</small> <br>
                    {{$partida->Pecabranca->titulo_em_uso() ? $partida->Pecabranca->titulo_em_uso()->Titulo->nome  : ' '}}
                </div>
                <div class="card-body">
                    <p>Pontuação: {{$partida->Pecabranca->pontuacao}}</p>
                </div>
                <div class="card-footer">
                    <p>Tempo Restante: <span id="tempoB"> {{date_format($partida->tempo_jogador_1, 'H:i:s')}} </span> </p>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
        <div class="card @if($partida->corPeca($usuario->id) == 'black') bg-dark @endif">
            <div class="card-body">
                <div id="myBoard" style="width: 100%;"></div>
                <div style="display: none">
                <label>Status:</label>
                    <div id="status"></div>
                    <label>FEN:</label>
                    <div id="fen"></div>
                    <label>PGN:</label>
                    <div id="pgn"></div>
                </div>
            </div>
            <div class="card-footer">
                <div>
                    <button id="solictaEmpate" disabled class="btn btn-warning text-white">Solicitar Empate</button>
                    <button id="informaredicao" class="btn btn-danger text-white float-right">Informar Rendição</button>
                </div>
            </div>
        </div>
        </div>

        <div class="col-lg-3">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <b>{{$partida->Pecapreta->apelido}}</b><small> ({{$partida->Pecapreta->nome}})</small> <br>
                    {{$partida->Pecapreta->titulo_em_uso() ? $partida->Pecapreta->titulo_em_uso()->Titulo->nome  : ' '}}
                </div>
                <div class="card-body">
                    <p>Pontuação: {{$partida->Pecapreta->pontuacao}}</p>
                </div>
                <div class="card-footer">
                    <p>Tempo Restante: <span id="tempoP"> {{date_format($partida->tempo_jogador_2, 'H:i:s')}} </span> </p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="escolhaPeca" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                   <div class="row">
                       <div class="col-md-3">
                           <div class="card peca" data-peca="q">
                               <div class="card-body">
                                   <img src="/img/chesspieces/wikipedia/wQ.png" width="100%" height="100%">
                               </div>
                               <div class="card-footer text-center">
                                    Rainha
                               </div>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="card peca" data-peca="r">
                               <div class="card-body" >
                                   <img src="/img/chesspieces/wikipedia/wR.png" width="100%" height="100%">
                               </div>
                               <div class="card-footer text-center">
                                   Torre
                               </div>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="card peca" data-peca="n">
                               <div class="card-body">
                                   <img src="/img/chesspieces/wikipedia/wN.png" width="100%" height="100%">
                               </div>
                               <div class="card-footer text-center">
                                   Cavalo
                               </div>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="card peca" data-peca="b">
                               <div class="card-body" >
                                   <img src="/img/chesspieces/wikipedia/wB.png" width="100%" height="100%">
                               </div>
                               <div class="card-footer text-center">
                                   Bispo
                               </div>
                           </div>
                       </div>
                   </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('css')
    <link rel="stylesheet"
          href="/css/chessboard-1.0.0.css">
@stop

@section('js')
    <script src="/js/socket.io.js"></script>
    <script src="/js/configuracao.js"></script>
    <script src="/js/chessboard-1.0.0.js"></script>
    <script>

        $( document ).ready(function() {
            var socket = io('http://localhost:7000');
            var myColorPiece = '{{$partida->inicialCorPeca($usuario->id)}}';
            var qtdSltEmpate = 3;
            var promoting = false;

            var object = {
                'auth' : {{$usuario->id}},
                'idSala' : {{$partida->id}},
                'jogador1' : {{$partida->jogador1($usuario->id)}}
            };

            socket.emit('entrarNaSala', object);

            socket.on('comunicacao',function(data){
                if(data.acao == "solicitaEmpate"){
                    Swal.fire({
                        title: 'O seu adversário solicita empate.',
                        text: "",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Aceitar',
                        cancelButtonText: 'Recusar',
                    }).then((result) => {
                        if (result.value) {
                            return socket.emit('solicitaEmpate', {'solicita': false, 'aceitou' : true});
                        }else{
                            return socket.emit('solicitaEmpate', {'solicita': false, 'aceitou' : false});
                        }
                    });
                }
                if(data.acao == "tempo"){
                    mudarTextTempo(data.tempoB, data.tempoP);
                }
                if(data.acao == "start"){
                    comecarPartida();
                }
                if(data.acao == "movimento"){
                    console.log("CHEGOU MOVIMENTO");
                    console.log(data.dados.source);
                    console.log(data);
                    game.move({
                        from: data.dados.source,
                        to: data.dados.target,
                        promotion: data.dados.promotion,
                    });
                    board.position(game.fen());
                    updateStatus();
                    game.setTurn(myColorPiece);
                    config.draggable = true;
                    if(qtdSltEmpate > 0){
                        $('#solictaEmpate').prop("disabled",false);
                    }
                }

                if(data.acao === "fimpartida"){
                    console.log("FINALIZANDO");
                    if(data.vencedor) {
                        Swal.fire(
                            'Fim da partida!',
                            'Vitória das ' + data.vencedor,
                            'success'
                        );
                    }
                    if(data.empate){
                        Swal.fire(
                            'Fim da partida!',
                            'EMPATE',
                            'success'
                        );
                    }
                    if(data.disconect){
                        Swal.fire(
                            'Fim da partida!',
                            'Vitória das ' + data.vencedor,
                            'success'
                        );
                    }

                        window.location.href = "/home";

                }
            });



            $('#solictaEmpate').click(function () {
                if(qtdSltEmpate > 0){
                    qtdSltEmpate = qtdSltEmpate - 1;
                    socket.emit('solicitaEmpate', {'solicita': true});
                    $('#solictaEmpate').prop("disabled",true);
                }
            });

            $('#informaredicao').click(function () {
                Swal.fire({
                    title: 'Deseja se render?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                }).then((result) => {
                    if (result.value) {
                        return socket.emit('informarendicao', {});
                    }
                });
            });


        function comecarPartida(){
            if(myColorPiece == 'w') {
                return config.draggable = true;
            }else {
                config.draggable = true;
                config.draggable = false;
            }
            console.log("PARTIDA COMEÇOU");
        }

        function onChange (oldPos, newPos) {
            console.log('Position changed:')
            console.log('Old position: ' + Chessboard.objToFen(oldPos))
            console.log('New position: ' + Chessboard.objToFen(newPos))
            console.log('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~')
        }

        function mudarTextTempo(tempoB, tempoP) {
            $("#tempoB").text(tempoB);
            $("#tempoP").text(tempoP);
        }

        var board = null
        var game = new Chess()
        var $status = $('#status')
        var $fen = $('#fen')
        var $pgn = $('#pgn')

        function onDragStart (source, piece, position, orientation) {
            // do not pick up pieces if the game is over
            if (game.game_over()) return false

            // only pick up pieces for the side to move
            if ((game.turn() === 'w' && piece.search(/^b/) !== -1) ||
                (game.turn() === 'b' && piece.search(/^w/) !== -1)) {
                return false
            }
        }

            function makeMove(game, cfg) {
                console.log("entrou make move");
                var move = game.move(cfg);
                // illegal move
                if (move === null) return 'snapback';

                updateStatus();
                config.draggable = false;
                $('#solictaEmpate').prop("disabled",true);
                console.log(move);
                socket.emit('movimentacao', {
                    'color': myColorPiece,
                    'source' : move.from,
                    'target' : move.to,
                    'fen' : game.fen(),
                    'promotion': move.promotion ?? 'q'
                });
                if(promoting){
                    board.position(game.fen(), false);
                    promoting = false;
                }
            }


         function onDrop (source, target) {
           move_cfg = {
               from: source,
               to: target,
               promotion: 'q'
           };

            var move = game.move(move_cfg);

            if(game.turn() == '{{$partida->inicialCorPeca($usuario->id)}}'){
                return 'snapback';
            }
            if (move === null){
                return 'snapback'
            }else{
                console.log('movimento ok : game.undo');
                game.undo();
           }

           var source_rank = source.substring(2,1);
           var target_rank = target.substring(2,1);
           var piece = game.get(source).type;

           if (piece === 'p' &&
               ((source_rank === '7' && target_rank === '8') || (source_rank === '2' && target_rank === '1'))) {
               promoting = true;

               const inputOptions = new Promise((resolve) => {
                       resolve({
                           'q': 'Rainha',
                           'b': 'Bispo',
                           'n': 'Cavalo',
                           'r': 'Torre',
                       });
               });

              Swal.fire({
                   title: 'Selecione uma promoção',
                   input: 'radio',
                   inputOptions: inputOptions,
                   inputValidator: (value) => {
                       move_cfg.promotion = value;
                       makeMove(game, move_cfg);
                   },
                  inputValue: 'q',
                  allowOutsideClick: false
              });

               return;
           }


            makeMove(game, move_cfg);

        }


        function onSnapEnd () {
            if (promoting) return;
            board.position(game.fen())
        }

        function updateStatus () {
            var status = ''

            var moveColor = 'White'
            if (game.turn() === 'b') {
                moveColor = 'Black'
            }

            // checkmate?
            if (game.in_checkmate()) {
                status = 'Game over, ' + moveColor + ' is in checkmate.'
            }

            // draw?
            else if (game.in_draw()) {
                status = 'Game over, drawn position'
            }

            // game still on
            else {
                status = moveColor + ' to move'

                // check?
                if (game.in_check()) {
                    status += ', ' + moveColor + ' is in check'
                }
            }

            $status.html(status)
            $fen.html(game.fen())
            $pgn.html(game.pgn())
        }


        var config = {
            draggable: false,
            position: 'start',
            onDragStart: onDragStart,
            onDrop: onDrop,
            onSnapEnd: onSnapEnd,
            orientation: '{{$partida->corPeca($usuario->id)}}',
        };
        var board = Chessboard('myBoard', config);

        board.start;
        });
    </script>
@stop