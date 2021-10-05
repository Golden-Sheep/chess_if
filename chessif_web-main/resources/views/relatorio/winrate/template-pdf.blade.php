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

<table>
    <thead>
    <tr class="table100-head">
        <th>Jogador</th>
        <th>Quantidade de partidas</th>
        <th>Quantidade de vitória</th>
        <th>Taxa de vitória</th>
    </tr>
    </thead>
    <tbody>
    @foreach($jogadores as $jogador)
        <tr>
            <td>{{$jogador['jogador']['nome']}} [{{$jogador['jogador']['apelido']}}]</td>
            <td>{{$jogador['qtdPartida']}}</td>
            <td>{{$jogador['vitorias']}}</td>
            <td>{{round($jogador['vitorias'] / $jogador['qtdPartida'] * 100)}}% </td>
        </tr>
    @endforeach
    </tbody>
</table>

<hr>



</body>
</html>

