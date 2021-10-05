@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@php( $login_url = View::getSection('login_url') ?? config('adminlte.login_url', 'login') )
@php( $register_url = View::getSection('register_url') ?? config('adminlte.register_url', 'register') )

@if (config('adminlte.use_route_url', false))
    @php( $login_url = $login_url ? route($login_url) : '' )
    @php( $register_url = $register_url ? route($register_url) : '' )
@else
    @php( $login_url = $login_url ? url($login_url) : '' )
    @php( $register_url = $register_url ? url($register_url) : '' )
@endif

@section('auth_header','Registrar uma nova conta')

@section('auth_body')
    <form action="{{ $register_url }}" method="post">
        {{ csrf_field() }}

        @if ($errors->has('register_message'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('register_message') }}
            </div>
        @endif
        {{-- Name field --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                   value="{{ old('name') }}" required placeholder="Digite seu nome inteiro" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('name'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('name') }}</strong>
                </div>
            @endif
        </div>

        <div class="input-group mb-3">
            <input type="text" name="apelido" class="form-control {{ $errors->has('apelido') ? 'is-invalid' : '' }}"
                   value="{{ old('apelido') }}" required placeholder="Digite um apelido" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-signature {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('apelido'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('apelido') }}</strong>
                </div>
            @endif
        </div>

        <div class="input-group mb-3">
            <select required id="selectUsuario" name="sexo" class="form-control select2" style="width: 100%;">
                <option value=""> Informe seu sexo ...</option>
                <option value="Masculino"> Masculino</option>
                <option value="Feminino"> Feminino</option>
                <option value="Prefiro não dizer">Prefiro não dizer</option>
            </select>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-venus-mars"></span>
                </div>
            </div>
            @if($errors->has('sexo'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('sexo') }}</strong>
                </div>
            @endif
        </div>

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                   value="{{ old('email') }}" required placeholder="Digite seu Email">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('email'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('email') }}</strong>
                </div>
            @endif
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" required
                   class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                   placeholder="Digite sua senha">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('password'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password') }}</strong>
                </div>
            @endif
        </div>

        {{-- Confirm password field --}}
        <div class="input-group mb-3">
            <input type="password" required name="password_confirmation"
                   class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                   placeholder="Digite novamente sua senha">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>
            @if($errors->has('password_confirmation'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </div>
            @endif
        </div>

        <div id="formIF">

            <div class="input-group mb-3">
                <input type="text" name="prontuario" required id="prontuario" class="form-control"
                       value="{{ old('prontuario') }}"  placeholder="Digite seu prontuário (EX: PE171xxxxx)" autofocus>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-id-card"></span>
                    </div>
                </div>
                @if($errors->has('prontuario'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('prontuario') }}</strong>
                    </div>
                @endif
            </div>

            <div class="input-group mb-3">
                <select  id="selectUsuario"  required name="campus" id="campus" class="form-control select2" style="width: 100%;">
                    <option value=""> Selecione seu campus ...</option>
                    @foreach($campus as $itens)
                        <option value="{{$itens->id}}"> {{$itens->sigla}} | {{$itens->nome}}</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-university"></span>
                    </div>
                </div>
                @if($errors->has('campus'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('campus') }}</strong>
                    </div>
                @endif
            </div>

        </div>


        {{-- Register button --}}
        <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
            <span class="fas fa-user-plus"></span>
            REGISTRAR
        </button>

    </form>
@stop

@section('auth_footer')
    <p class="my-0">
        <a href="/">
            Eu já tenho uma conta
        </a>
    </p>
@stop

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>


