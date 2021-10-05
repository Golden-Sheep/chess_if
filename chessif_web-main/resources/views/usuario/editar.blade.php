@extends('adminlte::page')

@section('title', 'Gerenciar - Usuários')

@section('content_header')
    <div class="row">
        <div class="col">
        <h1>Edição de Usuário </h1> <small>[{{$usuario->id}}]{{$usuario->nome}}</small>
        </div>
        <div class="col d-flex align-items-center justify-content-end">
        @if(!$usuario->banido)
            <a href="#" onclick="bloquear({{$usuario->id}})" class="btn btn-danger text-white" role="button"> Bloquear</a>
        @else
            <a href="#" onclick="desbloquear({{$usuario->id}})" class="btn btn-success text-white"  role="button"> Desbloquear</a>
        @endif
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
                <i class="fas fa-user"></i> Usuário
            </div>
            <form role="form" action="/usuarios/editar" method="post" autocomplete="off">
                {!! csrf_field() !!}
                <div class="card-body">
                    <input name="id" type="hidden" value="{{$usuario->id}}">
                    <div class="form-group">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                               value="{{ $usuario->nome }}" required placeholder="Ex: João da Silva" autofocus>
                        @if($errors->has('nome'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('nome') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label>Apelido</label>
                        <input type="text" name="apelido" class="form-control {{ $errors->has('apelido') ? 'is-invalid' : '' }}"
                               value="{{ $usuario->apelido }}" required placeholder="Ex: Batatinha" autofocus>
                        @if($errors->has('apelido'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('apelido') }}</strong>
                            </div>
                        @endif
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


                    <div class="form-group">
                        <label>Senha</label>
                        <input type="text" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               value="{{ $usuario->password }}" required placeholder="Digite a senha" autofocus>
                        @if($errors->has('password'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                        @endif
                    </div>


                    <div class="form-group">
                        <label>Prontuário </label>
                        <input type="text" name="prontuario" required class="form-control {{ $errors->has('prontuario') ? 'is-invalid' : '' }}"
                               value="{{ $usuario->prontuario }}"  placeholder="Digite o prontuario" autofocus>
                        @if($errors->has('prontuario'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('prontuario') }}</strong>
                            </div>
                        @endif
                    </div>

                    <div class="form-group">
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


                    <div class="form-group">
                        <label>Nivel de Acesso</label>
                        <select required name="nivel_de_acesso" class="form-control" >
                            <option value="2" @if($usuario->nivel_de_acesso == 2) selected @endif> Jogador</option>
                            <option value="1" @if($usuario->nivel_de_acesso == 1) selected @endif> Administrador</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <label>Campus</label>
                        <select name="campus"  required class="form-control" {{ $errors->has('campus') ? 'is-invalid' : '' }}>
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

                </div>
                <div class="card-footer">
                    <a href="{{$btnVoltar}}" class="btn btn-default">Cancelar</a>
                    <button type="submit" class="btn btn-primary float-right">Salvar</button>
                </div>
            </form>
        </div>


@stop

@section('css')
@stop

@section('js')
<script>

    function bloquear(id) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            title: 'Você tem certeza que deseja bloquear esse usuário?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, bloquear!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{route('bloquearUsuario')}}",
                    type: "post",
                    data: {id: id},
                    success: function (data, textStatus, xhr) {
                        if(xhr.status == 200) {
                            Swal.fire(
                                'Feito!',
                                'Usuário bloqueado!.',
                                'success'
                            );
                            setTimeout(() => {
                                window.location.href = "/usuarios";
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


    function desbloquear(id) {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        Swal.fire({
            title: 'Você tem certeza que deseja desbloquear esse usuário?',
            text: "",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, desbloquear!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: "{{route('desbloquearUsuario')}}",
                    type: "post",
                    data: {id: id},
                    success: function (data, textStatus, xhr) {
                        if(xhr.status == 200) {
                            Swal.fire(
                                'Feito!',
                                'Usuário desbloqueado!.',
                                'success'
                            );
                            setTimeout(() => {
                                window.location.href = "/usuarios";
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