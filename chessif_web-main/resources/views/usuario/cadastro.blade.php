@extends('adminlte::page')

@section('title', 'Gerenciar - Usuários')

@section('content_header')
    <h1>Cadastro de Usuário </h1> <small>Informe os dados abaixo;</small>
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
            <form role="form" action="/usuarios/cadastrar" method="post" autocomplete="off">
                {!! csrf_field() !!}
            <div class="card-body">

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" class="form-control {{ $errors->has('nome') ? 'is-invalid' : '' }}"
                           value="{{ old('nome') }}" required placeholder="Ex: João da Silva" autofocus>
                    @if($errors->has('nome'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('nome') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>Apelido</label>
                    <input type="text" name="apelido" class="form-control {{ $errors->has('apelido') ? 'is-invalid' : '' }}"
                           value="{{ old('nome') }}" required placeholder="Ex: Batatinha" autofocus>
                    @if($errors->has('nome'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('nome') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                           value="{{ old('email') }}" required placeholder="Digite o email" autofocus>
                    @if($errors->has('email'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('email') }}</strong>
                        </div>
                    @endif
                </div>


                <div class="form-group">
                    <label>Senha</label>
                    <input type="text" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                           value="{{ old('password') }}" required placeholder="Digite a senha" autofocus>
                    @if($errors->has('password'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('password') }}</strong>
                        </div>
                    @endif
                </div>


                <div class="form-group">
                    <label>Prontuário </label>
                    <input type="text" required name="prontuario" class="form-control {{ $errors->has('prontuario') ? 'is-invalid' : '' }}"
                           value="{{ old('prontuario') }}"  placeholder="Digite o prontuario" autofocus>
                    @if($errors->has('prontuario'))
                        <div class="invalid-feedback">
                            <strong>{{ $errors->first('prontuario') }}</strong>
                        </div>
                    @endif
                </div>

                <div class="form-group">
                <label>Sexo</label>
                <select required name="sexo" class="form-control" {{ $errors->has('sexo') ? 'is-invalid' : '' }}>
                    <option value=""> Selecione o sexo ...</option>
                    <option value="Masculino"> Masculino</option>
                    <option value="Feminino"> Feminino</option>
                    <option value="Prefiro não dizer">Prefiro não dizer</option>
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
                        <option value="2"> Jogador</option>
                        <option value="1"> Administrador</option>
                    </select>
                </div>


                <div class="form-group">
                    <label>Campus</label>
                    <select name="campus" class="form-control" {{ $errors->has('campus') ? 'is-invalid' : '' }}>
                        <option value=""> Selecione o campus ...</option>
                    @foreach($campus as $itens)
                        <option value="{{$itens->id}}"> {{$itens->sigla}} | {{$itens->nome}}</option>
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
                <button type="submit" class="btn btn-primary float-right">Cadastrar</button>
            </div>
            </form>
    </div>


@stop

@section('css')
@stop

@section('js')

@stop