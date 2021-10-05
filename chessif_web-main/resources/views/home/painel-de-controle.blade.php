@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')

@stop

@section('content')
    <h3>Servidor [Partidas] <i class="fas fa-signal" id="onlinePartida"></i></h3>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-server"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Consumo de CPU</span>
                    <span class="info-box-number" id="cpuUsageP"></span>

                    <div class="progress">
                        <div class="progress-bar" id="pCpuUsageP" style="width: 0%"></div>
                    </div>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-memory"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Consumo de Memória</span>
                    <span class="info-box-number" id="memoryUsageP"></span>

                    <div class="progress">
                        <div class="progress-bar" id="pMemoryUsageP" style="width: 0%"></div>
                    </div>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-user"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Usuários Conectados</span>
                    <span class="info-box-number" id="usuariosP">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>


    <h3>Servidor [Fila] <i class="fas fa-signal" id="onlineFila"></i></h3>
    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-server"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Consumo de CPU</span>
                    <span class="info-box-number" id="cpuUsageF"></span>

                    <div class="progress">
                        <div class="progress-bar" id="pCpuUsageF" style="width: 0%"></div>
                    </div>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-memory"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Consumo de Memória</span>
                    <span class="info-box-number" id="memoryUsageF"></span>

                    <div class="progress">
                        <div class="progress-bar" id="pMemoryUsageF" style="width: 0%"></div>
                    </div>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-dark">
                <span class="info-box-icon"><i class="fas fa-user"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Usuários Conectados</span>
                    <span class="info-box-number" id="usuariosF">0</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>
@stop

@section('css')
    <style>
        .Blink {
            animation: blinker 0.2s cubic-bezier(.5, 0, 1, 1) infinite alternate;
        }
        @keyframes blinker {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
        </style>
@stop

@section('js')
    <script src="/js/socket.io.js"></script>
    <script>
        $( document ).ready(function() {
            var socket = io('http://localhost:7000');
            var socket2 = io('http://localhost:9090');

            setInterval(() => {
            socket.emit('getConsumo', null);
            socket2.emit('getConsumo', null);
            }, 1500);

            socket.on('getConsumo',function(data){
                console.log(data);
                $("#cpuUsageP").text(data.cpu + "%");
                $("#pCpuUsageP").css("width", data.cpu+"px");

                $("#memoryUsageP").text((data.mUse - data.mFree) + "MB / " + data.mUse + "MB"  );
                $("#pMemoryUsageP").css("width", 100 - ((data.mFree / data.mUse) * 100)+"px");

                $("#usuariosP").text(data.usuarios);

            });

            socket2.on('getConsumo',function(data){
                console.log(data);
                $("#cpuUsageF").text(data.cpu + "%");
                $("#pCpuUsageF").css("width", data.cpu+"px");

                $("#memoryUsageF").text((data.mUse - data.mFree) + "MB / " + data.mUse + "MB"  );
                $("#pMemoryUsageF").css("width", 100 - ((data.mFree / data.mUse) * 100)+"px");

                $("#usuariosF").text(data.usuarios);

            });

            socket.on("disconnect", function(){
                $( "#onlineFila" ).removeClass( "Blink text-red" )
            });

            socket2.on('connect_error', function(){
                $( "#onlineFila" ).removeClass( "Blink text-red" )
            });

            socket2.on("connect", function(){
                $( "#onlineFila" ).addClass( "Blink text-red" );
            });

            socket2.on("disconnect", function(){
                $( "#onlinePartida" ).removeClass( "Blink text-red" )
            });

            socket.on('connect_error', function(){
                $( "#onlinePartida" ).removeClass( "Blink text-red" )
            });

            socket.on("connect", function(){
                $( "#onlinePartida" ).addClass( "Blink text-red" );
            });
        });
    </script>
@stop