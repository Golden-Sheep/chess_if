@extends('adminlte::page')

@section('title', 'Gerenciar - Títulos')

@section('content_header')
    <h1>Cadastro de Título </h1> <small>Informe os dados abaixo</small>
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
            <form role="form" action="/titulos/cadastrar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">

                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                               value="{{ old('nome') }}" required placeholder="Ex: Mestre" autofocus>
                        @if($errors->has('nome'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('nome') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea name="descricao" class="form-control {{ $errors->has('descricao') ? 'is-invalid' : '' }}"
                                   required placeholder="Ex: Possui 300 jogos ranqueado">{{ old('descricao') }}</textarea>
                        @if($errors->has('descricao'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('descricao') }}</strong>
                            </div>
                        @endif
                    </div>


                    <div class="form-group">
                        <input type="checkbox" name="metrica" value="1" @if(old('metrica')) checked @endif id="ativar">
                        <label>Quantidade de partidas jogadas</label>
                        <input type="text" id="inputQtd" name="qtd_partidas" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" disabled class="form-control {{ $errors->has('qtd_partidas') ? 'is-invalid' : '' }}"
                               value="{{ old('qtd_partidas') }}" required placeholder="Ex: 10" autofocus>
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right">Cadastrar</button>
                </div>
            </form>
        </div>


@stop

@section('css')
@stop

@section('js')
<script>
    $( document ).ready(function() {
        $('#ativar').change(function(){
                if ($(this).is(':checked')) {
                    $("#inputQtd").attr('disabled',false);
                }else{
                    $("#inputQtd").attr('disabled',true);
                }
            });
    });
</script>
@stop