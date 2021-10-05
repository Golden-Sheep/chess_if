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
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">

                        <div class="col-1 back" style="cursor: pointer;">
                            <i class="fas fa-backward"></i>
                        </div>

                        <div class="col-10">
                            @foreach($historicoObj as $item)
                                <span class="badge badge-secondary" id="badge{{$item->n_movimento}}">  {{$item->source}}  -> {{$item->target}}  </span>
                            @endforeach
                        </div>

                        <div class="col-1 next" style="cursor: pointer;">
                            <div class="float-right"> <i class="fas fa-forward"></i></div>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div id="myBoard" style="width: 100%;"></div>
                    <label>Status:</label>
                    <div id="status"></div>
                    <label>FEN:</label>
                    <div id="fen"></div>
                    <label>PGN:</label>
                    <div id="pgn"></div>
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
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet"
          href="/css/chessboard-1.0.0.css">

@stop

@section('js')
    <script src="/js/configuracao.js"></script>
    <script src="/js/chessboard-1.0.0.js"></script>
    <script>




        $( document ).ready(function() {
            var board = null
            var game = new Chess()
            var $status = $('#status')
            var $fen = $('#fen')
            var $pgn = $('#pgn')

            var movi = {!! $historico !!};
            var nMovi = -1;

            $(".next").click(function(e) {
                if(nMovi+1 < Object.keys(movi).length) {
                    $("#badge" + nMovi).toggleClass('badge-primary badge-secondary');
                    nMovi++;
                    $("#badge" + nMovi).toggleClass('badge-secondary badge-primary');

                    game.move({
                        from: movi[nMovi].source,
                        to:movi[nMovi].target,
                        //  promotion: 'q' // NOTE: always promote to a queen for example simplicity
                    });
                    board.position(game.fen());
                    updateStatus();

                }
            });

            $(".back").click(function(e) {
                if(nMovi-1 > -2) {
                    game.undo();
                    $("#badge" + nMovi).toggleClass('badge-secondary badge-primary');
                    nMovi--;
                    $("#badge" + nMovi).toggleClass('badge-primary badge-secondary');

                    board.position(game.fen());
                    updateStatus();

                }
            });


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

            function onDrop (source, target) {
                // see if the move is legal
                var move = game.move({
                    from: source,
                    to: target,
                    promotion: 'q' // NOTE: always promote to a queen for example simplicity
                })

                // illegal move
                if (move === null) return 'snapback'

                updateStatus()
            }

// update the board position after the piece snap
// for castling, en passant, pawn promotion
            function onSnapEnd () {
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
                onSnapEnd: onSnapEnd
            }
            board = Chessboard('myBoard', config)

            updateStatus()

        });
    </script>
@stop