@extends('adminlte::page')

@section('title', 'Gerenciar - Títulos')

@section('content_header')
    <div class="row">
        <div class="col">
            <h1>Edição de Título </h1> <small>{{$titulo->nome}}</small>
        </div>
        <div class="col d-flex align-items-center justify-content-end">
            <form action="/titulos/excluir" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="id" value="{{$titulo->id}}">
                <button type="submit" id="btn-ok" class="btn btn-danger">Excluir Título</button>
            </form>
        </div>
    </div>
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
                <i class="fas fa-medal"></i> Título
            </div>
            <form role="form" action="/titulos/editar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">
                    <input type="hidden" name="id" value="{{$titulo->id}}">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                               value="{{$titulo->nome}}" required placeholder="Ex: Mestre" autofocus>
                        @if($errors->has('nome'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('nome') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control {{ $errors->has('descricao') ? 'is-invalid' : '' }}"
                                  required placeholder="Ex: Possui 300 jogos ranqueado">{{$titulo->descricao}}</textarea>
                        @if($errors->has('descricao'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('descricao') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <input type="checkbox" name="metrica" value="1" @if($titulo->qtd_partida_necessaria > 0) checked @endif id="ativar">
                        <label>Quantidade de partidas jogadas</label>
                        <input type="text" @if(!$titulo->qtd_partida_necessaria > 0) disabled @endif id="inputQtd" name="qtd_partidas" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"  class="form-control {{ $errors->has('qtd_partidas') ? 'is-invalid' : '' }}"
                               value="{{ $titulo->qtd_partida_necessaria == 0 ? '' : $titulo->qtd_partida_necessaria }}" required placeholder="Ex: 10" autofocus>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right">Salvar</button>
                </div>
            </form>
        </div>


@stop

@section('css')
@stop

@section('js')
    <script>
        $('#btn-ok').on('click',function(e){
            e.preventDefault();
            let  form = $(this).parents('form:first');
            Swal.fire({
                title: 'Você tem certeza que deseja excluir esse título?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, excluir!'
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        });

        $('#ativar').change(function(){
            if ($(this).is(':checked')) {
                $("#inputQtd").attr('disabled',false);
            }else{
                $("#inputQtd").attr('disabled',true);
                $("#inputQtd").val('');
            }
        });
    </script>
@stop