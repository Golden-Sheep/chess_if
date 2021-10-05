@extends('adminlte::page')

@section('title', 'Gerenciar - Avisos')

@section('content_header')
    <h1>Cadastro de Aviso </h1> <small>Informe os dados abaixo</small>
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
                <i class="fas fa-exclamation-triangle"></i> Aviso
            </div>
            <form role="form" action="/avisos/cadastrar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">


                    <div class="form-group">
                        <label>Descrição</label>
                        <textarea  name="descricao" class="form-control {{ $errors->has('descricao') ? 'is-invalid' : '' }}"
                                   value="{{ old('descricao') }}" required placeholder="Ex: Olá, manutenção prevista para o dia ???? as ???"></textarea>
                        @if($errors->has('descricao'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('descricao') }}</strong>
                            </div>
                        @endif
                    </div>

                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary float-right">Enviar</button>
                </div>
            </form>
        </div>


@stop

@section('css')
@stop

@section('js')
    <script>
        $('form').submit(function() {
            $(this).find("button[type='submit']").prop('disabled',true);
        });
    </script>
@stop