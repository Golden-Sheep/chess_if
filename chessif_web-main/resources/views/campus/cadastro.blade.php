@extends('adminlte::page')

@section('title', 'Gerenciar - Campus')

@section('content_header')
    <h1>Cadastro de Campus </h1> <small>Informe os dados abaixo</small>
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
                <i class="fas fa-university"></i> Campus
            </div>
            <form role="form" action="/campus/cadastrar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">


                    <div class="form-group">
                        <label>Sigla</label>
                        <input type="text" name="sigla" class="form-control {{ $errors->has('sigla') ? 'is-invalid' : '' }}"
                               value="{{ old('sigla') }}" required placeholder="Ex: PEP" autofocus>
                        @if($errors->has('sigla'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('sigla') }}</strong>
                            </div>
                        @endif
                    </div>


                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                               value="{{ old('nome') }}" required placeholder="Ex: Presidente Epitácio" autofocus>
                        @if($errors->has('nome'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('nome') }}</strong>
                            </div>
                        @endif
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

@stop