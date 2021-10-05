@extends('adminlte::page')

@section('title', 'Melhores Jogadores')

@section('content_header')
    <h1>Melhores Jogadores </h1> <small>Jogadores com a maior pontuação</small>
@stop

@section('content')

    <div class="col-xs-12">
        @if ($errors->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('error') }}
            </div>
        @endif
        @php $p = 1; @endphp
        @foreach($usuario as $item)
        <div data-url="/perfil/{{$item->id}}" class="card linha-clicavel">
            <div class="card-body">
                <div class="row">
                    @if($p < 4)
                        @if($p == 1)
                        <img src="{{url('/img/coroas/rank1.svg')}}" style="max-width: 2%; max-height: 2%;">
                        @endif
                        @if($p == 2)
                            <img src="{{url('/img/coroas/rank2.svg')}}" style="max-width: 2%; max-height: 2%;">
                        @endif
                        @if($p == 3)
                            <img src="{{url('/img/coroas/rank3.svg')}}" style="max-width: 2%; max-height: 2%;">
                        @endif
                    @else
                    #<b>{{$p}}º</b>
                    @endif
                    <div class="col-sm">
                        {{$item->nome}} ({{$item->apelido}})
                    </div>
                    <div class="col-sm">
                        {{$item->titulo_em_uso() ? $item->titulo_em_uso()->Titulo->nome.' ('.$item->titulo_em_uso()->Titulo->descricao.')'  : 'N/A'}}
                    </div>
                    <div class="col-sm">
                        Pontuação: {{$item->pontuacao}}
                    </div>
                    <div class="col-sm">
                        Campus: {{$item->Campus->sigla}} - {{$item->Campus->nome}}
                    </div>
                </div>
            </div>
        </div>
            @php $p++; @endphp
        @endforeach

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


        $(".linha-clicavel").on("click", function () {
            window.location = $(this).data("url");
        })

        });
    </script>
@stop