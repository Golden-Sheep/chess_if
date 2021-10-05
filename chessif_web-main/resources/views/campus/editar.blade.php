@extends('adminlte::page')

@section('title', 'Gerenciar - Campus')

@section('content_header')

    <div class="row">
        <div class="col">
            <h1>Edição de Campus </h1> <small>[{{$campus->sigla}}]{{$campus->nome}}</small>
        </div>
        <div class="col d-flex align-items-center justify-content-end">
            <form action="/campus/excluir" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="id" value="{{$campus->id}}">
                <button type="submit" id="btn-ok" class="btn btn-danger">Excluir Campus</button>
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
                <i class="fas fa-university"></i> Campus
            </div>
            <form role="form" action="/campus/editar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">
                    <input type="hidden" name="id" value="{{$campus->id}}">

                    <div class="form-group">
                        <label>Sigla</label>
                        <input type="text" name="sigla" class="form-control {{ $errors->has('sigla') ? 'is-invalid' : '' }}"
                               value="{{$campus->sigla}}" required placeholder="Ex: PEP" autofocus>
                        @if($errors->has('sigla'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('sigla') }}</strong>
                            </div>
                        @endif
                    </div>


                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                               value="{{$campus->nome}}" required placeholder="Ex: Presidente Epitácio" autofocus>
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
    <script>

            $('#btn-ok').on('click',function(e){
                e.preventDefault();
                let  form = $(this).parents('form:first');
                Swal.fire({
                    title: 'Você tem certeza que deseja excluir esse campus?',
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


    </script>
@stop