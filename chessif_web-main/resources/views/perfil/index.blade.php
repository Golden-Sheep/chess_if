@extends('adminlte::page')

@section('title', 'Perfil de '.$usuario->nome.'('.$usuario->apelido.')')

@section('content_header')
    <h1>Perfil de {{$usuario->nome}} ({{$usuario->apelido}})</h1>
    <small>Informações públicas fornecida pelo sistema</small>
@stop

@section('content')

    <div class="col-xs-12">
        @if ($errors->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('error') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                Posição no ranking:
                @if($p < 4)
                    @if($p == 1)
                        <img src="{{url('/img/coroas/rank1.svg')}}" style="max-width: 2%; max-height: 2%;">
                    @endif
                    @if($p == 2)
                        <img src="{{url('/img/coroas/rank2.svg')}}" style="max-width: 2%; max-height: 2%;">
                    @endif
                    @if($p == 3)
                        <img src="{{url('/img/coroas/rank3.svg')}}" style="max-width: 2%; max-height: 2%;">
                    @endif
                @else
                    #<b>{{$p}}º</b>
                @endif
            </div>
            <div class="card-body">
                    <div class="col">
                        <div class="form-group">
                            <label>Nome</label>
                            {{ $usuario->nome }} ({{ $usuario->apelido }})
                        </div>
                        <div class="form-group">
                            <label>Prontuário</label>
                            {{$usuario->prontuario}}
                        </div>
                        <div class="form-group">
                            <label>Sexo</label>
                            {{$usuario->sexo}}
                        </div>
                        <div class="form-group">
                            <label>Campus</label>
                            {{ $usuario->Campus->sigla }} - {{ $usuario->Campus->nome }}
                        </div>
                        <div class="form-group">
                            <label>Título</label>
                            {{$usuario->titulo_em_uso() ? $usuario->titulo_em_uso()->Titulo->nome.' ('.$usuario->titulo_em_uso()->Titulo->descricao.')'  : 'N/A'}}
                        </div>
                    </div>
            </div>
            <div class="card-footer">
                <a href="{{$btnVoltar}}" class="btn btn-default">Voltar</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Histórico de partidas
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tabela">
                    <thead>
                    <tr>
                        <th>Nº Partida</th>
                        <th>Peça Branca [Pontuação]</th>
                        <th>Peça Preta [Pontuação]</th>
                        <th>Status</th>
                        <th>Motivo</th>
                        <td>Data</td>
                        <th>Modo</th>
                        <th>Replay</th>
                    </tr>
                    </thead>
                    <tbody id="bodytabela">
                    @foreach($partida as $item)
                        <tr data-url="#" class="linha-clicavel">
                            <td>{{$item->id}}</td>
                            <td>{{$item->pecabranca->nome}} ({{$item->pecabranca->apelido}}) [{{$item->pontuacao_jogador_1}}]</td>
                            <td>{{$item->pecapreta->nome}} ({{$item->pecapreta->apelido}}) [{{$item->pontuacao_jogador_2}}]</td>
                            <td>
                                @if(!$item->ganhador)
                                    <span class="badge badge-warning">  EMPATE </span>
                                @else
                                    @if($usuario->id == $item->ganhador)
                                        <span class="badge badge-success">  VITÓRIA </span>
                                    @else
                                        <span class="badge badge-danger">  DERROTA </span>
                                    @endif
                                @endif

                            </td>
                            <td>{{$item->motivo}}</td>
                            <td>{{date_format($item->created_at, 'd/m/Y H:i')}}</td>
                            <td><span class="badge badge-secondary"> {{$item->modo}} </span></td>
                            <td>
                                <a class="badge badge-dark" href="/partida/replay/{{$item->id}}"><span>Assistir <i class="fas fa-reply-all"></i></span></a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>


@stop

@section('css')
    <style>
        .linha-clicavel:hover{
            cursor: pointer;
            transform: scale(1.005);
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function(){

            $("#tabela").DataTable({
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });

        });

        $("#bodytabela").on("click", "tr[data-url]", function () {
            window.location = $(this).data("url");
        });
    </script>
@stop