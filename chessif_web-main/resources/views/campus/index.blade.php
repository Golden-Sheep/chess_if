@extends('adminlte::page')

@section('title', 'Gerenciar - Campus')

@section('content_header')
    <h1>Gerenciar ⇨ Campus </h1> <small>Todos os campus cadastrados</small>
@stop

@section('content')

    <div class="col-xs-12">
        @if ($errors->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('error') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header with-border">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-lg-3">
                        <a href="/campus/cadastrar">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>Novo</h3>
                                    <p>Campus</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <span class="small-box-footer">Cadastrar <i class="fa fa-arrow-circle-right"></i></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="tabela">
                    <thead>
                    <tr>
                        <th>Sigla</th>
                        <th>Campus</th>
                    </tr>
                    </thead>
                    <tbody id="bodytabela">
                    @foreach($campus as $item)
                        <tr data-url="/campus/editar/{{$item->id}}" class="linha-clicavel">
                            <td>{{$item->sigla}}</td>
                            <td>{{$item->nome}}</td>
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