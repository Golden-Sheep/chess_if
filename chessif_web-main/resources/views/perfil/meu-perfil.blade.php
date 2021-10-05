@extends('adminlte::page')

@section('title', 'Meu Perfil')

@section('content_header')
    <h1>Meu Perfil </h1> <small>Visualize e edite suas informações</small>
@stop

@section('content')

    <div class="col-xs-12">
        @if ($errors->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('error') }}
            </div>
        @endif
    <div class="card">
        <div class="card-body">
                <form role="form" action="/editar/perfil" method="post" autocomplete="off">
                    {!! csrf_field() !!}
                    <div class="row">
                        <div class="form-group col-6">
                            <label>Nome</label>
                            <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                                   value="{{ $usuario->nome }}" required placeholder="Ex: João da Silva" autofocus>
                            @if($errors->has('nome'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('nome') }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="form-group col-6">
                            <label>Apelido</label>
                            <input type="text" name="apelido" class="form-control {{ $errors->has('apelido') ? 'is-invalid' : '' }}"
                                   value="{{ $usuario->apelido }}" required placeholder="Ex: Batatinha" autofocus>
                            @if($errors->has('apelido'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('apelido') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                                   value="{{ $usuario->email }}" required placeholder="Digite o email" autofocus>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </div>
                            @endif
                        </div>

                    <div class="row">
                        <div class="form-group col-4">
                            <label>Senha</label>
                            <input type="text" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                   value="{{ $usuario->password }}" required placeholder="Digite a senha" autofocus>
                            @if($errors->has('password'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </div>
                            @endif
                        </div>


                        <div class="form-group col-4">
                            <label>Prontuário </label>
                            <input type="text" name="prontuario" required class="form-control {{ $errors->has('prontuario') ? 'is-invalid' : '' }}"
                                   value="{{ $usuario->prontuario }}"  placeholder="Digite o prontuario" autofocus>
                            @if($errors->has('prontuario'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('prontuario') }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="form-group col-4">
                            <label>Sexo</label>
                            <select required name="sexo" class="form-control" {{ $errors->has('sexo') ? 'is-invalid' : '' }}>
                                <option value="">  Informe seu sexo  ...</option>
                                <option value="Masculino" @if($usuario->sexo == "Masculino") selected @endif> Masculino</option>
                                <option value="Feminino" @if($usuario->sexo == "Feminino") selected @endif> Feminino</option>
                                <option value="Prefiro não dizer" @if($usuario->sexo == "Prefiro não dizer") selected @endif>Prefiro não dizer</option>
                            </select>
                            @if($errors->has('sexo"'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('sexo"') }}</strong>
                                </div>
                            @endif
                        </div>

                    </div>



                    <div class="row">
                        <div class="form-group col-6">
                            <label>Campus</label>
                            <select name="campus" required class="form-control" {{ $errors->has('campus') ? 'is-invalid' : '' }}>
                                <option value=""> Selecione o campus ...</option>
                                @foreach($campus as $itens)
                                    <option value="{{$itens->id}}" @if($itens->id == $usuario->campus) selected @endif> {{$itens->sigla}} | {{$itens->nome}}</option>
                                @endforeach
                            </select>
                            @if($errors->has('campus'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('campus') }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="form-group col-6">
                            <label>Título</label>
                            <select name="titulo" class="form-control" {{ $errors->has('titulo') ? 'is-invalid' : '' }}>
                                <option value=""> Selecione um título ...</option>
                                @foreach($titulo as $itens)
                                    <option value="{{$itens->Titulo->id}}" @if($itens->ativo) selected @endif> {{$itens->Titulo->nome}} ({{$itens->Titulo->descricao}})  </option>
                                @endforeach
                            </select>
                            @if($errors->has('titulo'))
                                <div class="invalid-feedback">
                                    <strong>{{ $errors->first('titulo') }}</strong>
                                </div>
                            @endif
                        </div>

                    </div>

                    </div>
                    <div class="card-footer">
                        <a href="{{$btnVoltar}}" class="btn btn-default">Cancelar</a>
                        <button type="submit" class="btn btn-primary float-right">Salvar</button>
                    </div>
                </form>
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
                        <th>Data</th>
                        <th>Modo</th>
                        <th>Replay</th>
                    </tr>
                    </thead>
                    <tbody id="bodytabela">
                    @foreach($partida as $item)
                        <tr >
                            <td>{{$item->id}}</td>
                            <td>{{$item->pecabranca->nome}} ({{$item->pecabranca->apelido}}) [{{$item->pontuacao_jogador_1}}]</td>
                            <td>{{$item->pecapreta->nome}} ({{$item->pecapreta->apelido}}) [{{$item->pontuacao_jogador_2}}]</td>
                            <td>
                                @if(!$item->ganhador)
                                    <span class="badge badge-warning ">  EMPATE </span>
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