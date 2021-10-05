@extends('adminlte::page')

@section('title', 'Jogador - Gerenciar Títulos')

@section('content_header')
    <h1>Gerenciar Títulos  | Adicionar ou Remover </h1> <small>{{$usuario->nome}} ({{$usuario->apelido}})</small>
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
                <i class="fas fa-medal"></i> <i class="fas fa-user"></i> {{$usuario->nome}} ({{$usuario->apelido}})Título

            </div>
            <div class="card-body">
                <table class="table table-striped" id="tabela">
                    <thead>
                    <tr>
                        <th><i class="fas fa-medal"></i> Nome</th>
                        <th><i class="fas fa-medal"></i> Descrição</th>
                        <th><i class="fas fa-medal"></i> <i class="fas fa-user"></i> Ação</th>
                    </tr>
                    </thead>
                    <tbody id="bodytabela">
                    @foreach($titulos as $item)
                        <tr>
                            <td>{{$item->nome}}</td>
                            <td>{{$item->descricao}}</td>
                            <td>
                                @if($usuario->lista_titulos->where('titulo', $item->id)->count())
                                    <a href="#" onclick="remover({{$item->id}}, {{$usuario->id}})" class="btn  btn-danger text-white float-right"> Remover</a>
                                @else
                                    <a href="#" onclick="adicionar({{$item->id}}, {{$usuario->id}})" class="btn  btn-primary text-white float-right"> Adicionar</a>
                                @endif
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

@stop

@section('js')
    <script>
        var url = '/gerenciartitulo/'+{{$usuario->id}};
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


        function adicionar(idTitulo, jogador){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Você tem certeza que deseja adicionar esse título?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, adicionar!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{route('adicionarTitulo')}}",
                        type: "post",
                        data: {titulo: idTitulo, jogador: jogador},
                        success: function (data, textStatus, xhr) {
                            if(xhr.status == 200) {
                                Swal.fire(
                                    'Feito!',
                                    'Título adicionado!.',
                                    'success'
                                );
                                setTimeout(() => {
                                    window.location.href = url;
                                }, 1500);

                            }else{
                                Swal.fire(
                                    'Algo deu errado!',
                                    'Atualize a pagina e tente novamente!.',
                                    'error'
                                );
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);

                        }
                    });
                }
            });

        }

        function remover(idTitulo, jogador){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                title: 'Você tem certeza que deseja remover esse título?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, remover!'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "{{route('removerTitulo')}}",
                        type: "post",
                        data: {titulo: idTitulo, jogador: jogador},
                        success: function (data, textStatus, xhr) {
                            if(xhr.status == 200) {
                                Swal.fire(
                                    'Feito!',
                                    'Título removido!.',
                                    'success'
                                );
                                setTimeout(() => {
                                    window.location.href = url;
                                }, 1500);

                            }else{
                                Swal.fire(
                                    'Algo deu errado!',
                                    'Atualize a pagina e tente novamente!.',
                                    'error'
                                );
                            }
                        },
                        error: function (data) {
                            console.log('Error:', data);

                        }
                    });
                }
            });

        }

    </script>
@stop