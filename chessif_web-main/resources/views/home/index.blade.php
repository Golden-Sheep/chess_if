@extends('adminlte::page')

@section('title', 'Salão de Espera')

@section('content_header')

@stop

@section('content')
    @if ($errors->has('error'))
        <div class="alert alert-danger" role="alert">
            {{ $errors->first('error') }}
        </div>
    @endif
    <div class="alert alert-danger" id="servidorFilaOff" style="display: none;" role="alert">
        Servidor de Fila [Falha na conexão] <br>
        Atualize a pagina e tente novamente.
    </div>
    <div class="alert alert-danger" id="servidorJogoOff" style="display: none;" role="alert">
        Servidor de Jogo [Falha na conexão]   <br>
        Atualize a pagina e tente novamente.
    </div>
    <div class="row">
        <!-- CARD ENTRAR FILA RANQUEADA -->
        <div class="col-lg-6">
            <div class="card" style="height: 30rem;">
                <div class="card-header">
                    <b>Ranqueada</b><br>
                    Jogue com adversários do mesmo nível, ganhe partidas para conseguir pontos de classificação de nível.
                </div>
                <div class="card-body">
                    <img src="{{url('/img/icon_ranqueado.svg')}}" style="max-width: 100%; max-height: 100%;" class="d-block mx-auto">
                </div>
                <div class="card-footer">
                    <center>
                        <div id="procurandoRanqueada"style="pointer-events: none; display: none;" class="btn btn-block btn-primary"> Procurando Adversário   <i class="fas fa-spinner fa-pulse"></i></div>
                        <button id="btnJoinRanqueada" onclick="entrarNaFila('{{$token}}')" class="btn btn-block btn-success"> Entrar na Fila Ranqueada  </button>
                        <button  style=" display: none;" onclick="sairFila()" id="btnLeftRanqueada"  class="btn btn-block btn-danger"> Sair da Fila Ranqueada   </button>
                    </center>
                </div>
            </div>
        </div>

        <!-- CARD ENTRAR FILA CASUAL -->
        <div class="col-lg-6">
            <div class="card" style="height: 30rem;">
                <div class="card-header">
                    <b>Casual</b><br>
                    Jogue partidas casuais, com jogadores de qualquer nível.<br> &nbsp
                </div>
                <div class="card-body">
                    <img src="{{url('/img/icon_casual.svg')}}" style="max-width: 100%; max-height: 100%;" class="d-block mx-auto">
                </div>
                <div class="card-footer">
                    <center>
                        <div id="procurandoCasual"style="pointer-events: none; display: none;" class="btn btn-block btn-primary"> Procurando Adversário   <i class="fas fa-spinner fa-pulse"></i></div>
                        <button   id="btnJoinCasual" onclick="entrarNaFilaCasual('{{$token}}')" class="btn btn-block btn-success"> Entrar na Fila Casual  </button>
                        <button  style=" display: none;" onclick="sairFilaCasual()" id="btnLeftCasual"  class="btn btn-block btn-danger"> Sair da Fila Casual   </button>
                    </center>
                </div>
            </div>
        </div>


        <!-- CHAT -->
        <div class="col-lg-8">
        <div class="card direct-chat direct-chat-primary">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">Chat</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="direct-chat-messages" id="corpoChat">

                </div>
            </div>
            <div class="card-footer">
                <form id="formMsg">
                    <div class="input-group">
                        <input type="text" minlength="1" maxlength="300" required id="corpoMsg" placeholder="Digite aqui ..." class="form-control socket">
                        <span class="input-group-append">
                      <button type="submit"  class="btn btn-primary socket">Enviar</button>
                    </span>
                    </div>
                </form>
        </div>
        </div>
    </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                Jogadores Online no Salão de Espera
                </div>
                <div class="card-body">
                    <div class="card" id="listaDeJogador">

                    </div>
                </div>
            </div>
        </div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script src="/js/socket.io.js"></script>
    <script>
        var socket = io('http://localhost:9090');
        const nome = '{{$token->nome}} ({{$token->apelido}})';
        const meuId = {{$token->id}};

        socket.on('comunicacao', function (data) {
            console.log(data);
            if(data.msg === "Partida Criada"){
                window.location.href = '/partida/'+data.idPartida;
            }

            if(data.msg === "Usuario ja esta na fila"){
                alert(data.msg);
                $("#btnJoinRanqueada").show();
                $("#procurandoRanqueada").hide();
                $("#btnLeftRanqueada").hide();
            }

            if(data.msg == "Servidor de jogo indisponivel"){
                $("#btnJoinRanqueada").show();
                $("#procurandoRanqueada").hide();
                $("#btnLeftRanqueada").hide();
                $("#servidorJogoOff").show();
            }


        });

        function estouDisponivel(){
            socket.emit('estouDisponivel', {'id': meuId, 'nome' : nome});
        }

        function estouIndisponivel(){
            socket.emit('estouIndisponivel', {'id': meuId});
        }

        function desafiar(socketId, id){
            socket.emit('desafiar', {'id' : id, 'socketId' : socketId, 'nome': nome});
        }

        socket.on('desafio', function (data) {
            Swal.fire({
                title: 'Você foi desafiado para uma partida casual pelo jogador '+data.nome,
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceitar desafio!',
                cancelButtonText: 'Recusar desafio!',
            }).then((result) => {
                if (result.value) {
                    socket.emit('gerenciarDesafio', {'statusDesafio' : 'aceito', 'info': data});
                }
            });
        });


        socket.on("gerenciarJogador", function(data){
            console.log(data);
           if(data){
               $("#listaDeJogador").empty();
               for (var i = 0; i < Object.keys(data.jogador).length; i++) {
                   if (data.jogador[i].id !=  meuId) {

                       $("#listaDeJogador").append('<div id="jogador'+data.jogador[i].id+'" class="card-body">\n' +
                           '                           <div class="d-flex  align-items-center justify-content-between">\n' +
                           '                                    '+data.jogador[i].nome+
                           '                                    <button onclick="desafiar(\'' +  data.jogador[i].socket + '\' , \'' +  data.jogador[i].id + '\' )" class="btn btn-info desafiar">\n' +
                           '                                    Desafiar\n' +
                           '                                    </button>\n' +
                           '                            </div>\n' +
                           '                        </div>');
                   }
               }
           }
        });

        socket.on("chat", function(data){
            console.log(data);
            $("#corpoChat").append('                    <div class="direct-chat-msg">\n' +
                '                        <div class="direct-chat-infos clearfix">\n' +
                '                            <span class="direct-chat-name float-left">'+data.nome+'</span>\n' +
                '                        </div>\n' +
                '                        <img class="direct-chat-img" src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" alt="message user image">\n' +
                '                        <div class="direct-chat-text">\n' +
                '                            '+ data.msg+
                '                        </div>\n' +
                '                    </div>');

            $('#corpoChat').stop ().animate ({
                scrollTop: $('#corpoChat')[0].scrollHeight
            });
        });

        socket.on("disconnect", function(){
            $(".socket").prop('disabled', true);
            setTimeout($("#servidorFilaOff").show(), 10000);
        });

        socket.on('connect', () => {
            socket.emit('desclararId', meuId);
            $(".socket").prop('disabled', false);
            $("#servidorFilaOff").hide();
        });

        socket.on('connect_error', function(){
            $(".socket").prop('disabled', true);
            setTimeout($("#servidorFilaOff").show(), 5000);
        });

        function entrarNaFila(idAuth){

            socket.emit('entrarNaFila', idAuth);

            $("#btnJoinRanqueada").hide();
            $("#procurandoRanqueada").show();
            $("#btnLeftRanqueada").show();
            $("#btnJoinCasual").prop('disabled', true);
        }

        function entrarNaFilaCasual(idAuth) {
            socket.emit('entrarNaFilaCasual', idAuth);

            $("#btnJoinCasual").hide();
            $("#procurandoCasual").show();
            $("#btnLeftCasual").show();
            $("#btnJoinRanqueada").prop('disabled', true);
        }

        function sairFila(){
            socket.emit('sairDaFila', '');
            $("#btnJoinRanqueada").show();
            $("#procurandoRanqueada").hide();
            $("#btnLeftRanqueada").hide();
            $("#btnJoinCasual").prop('disabled', false);
        }

        function sairFilaCasual() {
            socket.emit('sairDaFila', '');
            $("#btnJoinCasual").show();
            $("#procurandoCasual").hide();
            $("#btnLeftCasual").hide();
            $("#btnJoinRanqueada").prop('disabled', false);
        }

        function enviarMsg(){

            var msg = $("#corpoMsg").val();
            socket.emit('enviarMsg', { nome: nome, 'msg': msg});
            $("#corpoChat").append('<div class="direct-chat-msg right">\n' +
                '                        <div class="direct-chat-infos clearfix">\n' +
                '                            <span class="direct-chat-name float-right">'+nome+'</span>\n' +
                '                        </div>\n' +
                '                        <img class="direct-chat-img" src="https://cdn4.iconfinder.com/data/icons/small-n-flat/24/user-alt-512.png" alt="message user image">\n' +
                '                        <div class="direct-chat-text">\n' +
                '                            '+msg+
                '                        </div>\n' +
                '                    </div>');
            $("#corpoMsg").val('');
            $('#corpoChat').stop ().animate ({
                scrollTop: $('#corpoChat')[0].scrollHeight
            });
        }


        $(document).ready(function(){
            estouDisponivel();

            $('#formMsg').on('submit', function(e) {
                e.preventDefault();
                enviarMsg();
            });

        });


    </script>
@stop