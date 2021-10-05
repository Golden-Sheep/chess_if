@extends('adminlte::page')

@section('title', 'Meus Avisos')

@section('content_header')
    <h1>Meus Avisos </h1>
@stop

@section('content')

    <div class="col-xs-12">
    @foreach($aviso as $item)
        <div class="card">
            <div class="card-header">{{date_format($item->created_at,'d/m/Y - H:i:s')}}</div>
            <div class="card-footer">{{$item->texto}}</div>
        </div>
        @endforeach
    </div>


@stop

@section('css')

@stop

@section('js')

@stop