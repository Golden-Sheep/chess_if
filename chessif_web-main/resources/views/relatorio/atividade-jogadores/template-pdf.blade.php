<!DOCTYPE html>
<html lang="en">
<head>
    <title>Relatório</title>
    <meta charset="UTF-8">

    <style>
        * {
            margin: 0px;
            padding: 0px;
            box-sizing: border-box;
        }
        body, html {
            height: 100%;
            font-family: sans-serif;
        }
        h1,h2,h3,h4,h5,h6 {margin: 0px;}
        p {margin: 0px;}
        .limiter {
            width: 100%;
            margin: 0 auto;
        }
        .wrap-table100 {
            width: 1170px;
        }
        table {
            border-spacing: 1;
            border-collapse: collapse;
            background: white;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            overflow: hidden;
            width: 100%;
            margin: 0 auto;
            position: relative;
        }
        table td, table th {
            padding-left: 8px;
        }
        table thead tr {
            height: 30px;
            background: #333;
        }
        table tbody tr {
            height: 20px;
        }
        table tbody tr:last-child {
            border: 0;
        }
        table td, table th {
            text-align: left;
        }
        table td.l, table th.l {
            text-align: right;
        }
        table td.c, table th.c {
            text-align: center;
        }
        table td.r, table th.r {
            text-align: center;
        }
        .table100-head th{
            font-family: sans-serif;
            font-size: 13px;
            color: #fff;
            line-height: 1;
            font-weight: unset;
        }
        tbody tr:nth-child(even) {
            background-color: #cccccc;
        }
        tbody tr {
            font-family: Sans-serif;
            font-size: 11px;
            color: #000000;
            line-height: 1;
            font-weight: unset;
        }
    </style>
</head>
<body>
<table>
    <tr>
        <td style="width:900px"><h1>{{$titulo}}</h1><br><h4>{{date_format(date_create($datainicio), 'd/m/Y')}} à {{date_format(date_create($datafim), 'd/m/Y')}}</h4></td>
    </tr>
</table>
<hr>
<br>
@foreach($jogadores as $jogador)
<table>
    <thead>
    <tr class="table100-head">
        <th>Jogador {{$jogador['usuario']['nome']}} [{{$jogador['usuario']['apelido']}}] - Taxa de Vitória: @if($jogador['winrate']){{$jogador['winrate']}}% @else ?? @endif </th>
    </tr>
    <tr class="table100-head">
        <th>Partidas</th>
    </tr>
    </thead>
</table>
<table>
    @if(sizeof($jogador['partidas']) > 0)
    <tr>
        <th>Nº Partida</th>
        <th>Peça Branca [Pontuação]</th>
        <th>Peça Preta [Pontuação]</th>
        <th>Status</th>
        <th>Motivo</th>
        <th>Data</th>
        <th>Modo</th>
    </tr>
    @endif
    @foreach($jogador['partidas'] as $partida)
        <tr>
            <td>{{$partida->id}}</td>
            <td>{{$partida->pecabranca->nome}} ({{$partida->pecabranca->apelido}}) [{{$partida->pontuacao_jogador_1}}]</td>
            <td>{{$partida->pecapreta->nome}} ({{$partida->pecapreta->apelido}}) [{{$partida->pontuacao_jogador_2}}]</td>
            <td>
                @if(!$partida->ganhador)
                    <span class="badge badge-warning ">  EMPATE </span>
                @else
                    @if($jogador['usuario']['id'] == $partida->ganhador)
                        <span class="badge badge-success">  VITÓRIA </span>
                    @else
                        <span class="badge badge-danger">  DERROTA </span>
                    @endif
                @endif
            </td>
            <td>{{$partida->motivo}}</td>
            <td>{{date_format($partida->created_at, 'd/m/Y H:i')}}</td>
            <td><span class="badge badge-secondary"> {{$partida->modo}} </span></td>
        </tr>
    @endforeach
</table>
    <br>
@endforeach



</body>
</html>

