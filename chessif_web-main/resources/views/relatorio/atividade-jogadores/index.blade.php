@extends('adminlte::page')
@section('title', 'ChessIF | Relatórios')
@section('content_header')
    <h1>Relatório ⇨ Atividade de novos jogadores  </h1>
@stop
@section('content')
    <div class="col-md-12">
        @if ($errors->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ $errors->first('error') }}
            </div>
        @endif
        <div class="card">
            <div class="card-header with-border">
                <h3 class="card-title">Relatório de atividade de novos jogadores por um determinado período</h3>
                <label class="float-right"> <i class="fas fa-user"></i></label>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group {{ $errors->has('data_de_atendimento') ? 'has-error' : '' }}">
                            <label>Período em dias</label>
                            <input type="text" class="form-control pull-right" readonly id="data_de_atendimento" name="data_de_atendimento" value="{{ old('data_de_atendimento') }}">
                        </div>
                        @if ($errors->has('data_de_atendimento'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('data_de_atendimento') }}</strong>
                                    </span>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Opções</label>
                            <select class="form-control pull-right" id="opcoes">
                                <option value="0" selected> Selecione um período </option>
                                <option value="7"> Últimos 7 dias</option>
                                <option value="15"> Últimos 15 dias</option>
                                <option value="30"> Últimos 30 dias</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
            <div class="card-footer">
                <a  id="btnPdf" onclick="exportarPdf()" class="btn btn-flat btn-default float-right">Gerar PDF &nbsp;<i class="far fa-file-pdf"></i></a>
            </div>
        </div>
    </div>

@stop
@section('css')
@stop
@section('js')
    <script type="text/javascript" src="/js/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/daterangepicker.css" />
    <script type="text/javascript" src="/js/moment.min.js"></script>
    <script>

        $("#opcoes").change(function () {
            var now = moment();
            now = now.format("DD/MM/YYYY");


            if($(this).val() == 0){
                $("#data_de_atendimento").val('');
            }else {
                startdate = moment();
                if ($(this).val() == 7) {

                    startdate = startdate.subtract(7, 'd');
                    startdate = startdate.format('DD/MM/YYYY');
                    console.log(startdate);
                }
                if ($(this).val() == 15) {

                    startdate = startdate.subtract(15, "days");
                    startdate = startdate.format("DD/MM/YYYY");
                }
                if ($(this).val() == 30) {

                    startdate = startdate.subtract(30, "days");
                    startdate = startdate.format("DD/MM/YYYY");
                }

                $("#data_de_atendimento").val(startdate+' - '+now);
            }
        });

        $(function() {
            $('#data_de_atendimento').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    "daysOfWeek": [
                        "Dom",
                        "Seg",
                        "Ter",
                        "Qua",
                        "Qui",
                        "Sex",
                        "Sab"
                    ],
                    "monthNames": [
                        "Janeiro",
                        "Fevereiro",
                        "Março",
                        "Abril",
                        "Maio",
                        "Junho",
                        "Julho",
                        "Agosto",
                        "Setembro",
                        "Outubro",
                        "Novembro",
                        "Dezembro"
                    ],
                    "applyLabel": "Aplicar",
                    "cancelLabel": "Cancelar",
                },

            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
                $('#data_de_atendimento').val(start.format('DD/MM/YYYY')  + ' - ' +  end.format('DD/MM/YYYY'))
            });

        });

        function exportarPdf() {
            let val =  $('#data_de_atendimento').val();
            if(val){
                let split = val.split('-');
                var dataInicial = moment($.trim(split[0]), 'DD/MM/YYYY');
                dataInicial = dataInicial.format('YYYY-MM-DD');

                var dataFinal = moment($.trim(split[1]), 'DD/MM/YYYY');
                dataFinal = dataFinal.format('YYYY-MM-DD');
                window.open('/relatorio/jogadores/atividade/pdf/'+dataInicial+'/'+dataFinal, '_blank');

            }else{
                console.log('q');
            }
        }

        function exportarXlxs() {
            let val =  $('#data_de_atendimento').val();
            if(val){
                let split = val.split('-');
                var dataInicial = moment($.trim(split[0]), 'DD/MM/YYYY');
                dataInicial = dataInicial.format('YYYY-MM-DD');

                var dataFinal = moment($.trim(split[1]), 'DD/MM/YYYY');
                dataFinal = dataFinal.format('YYYY-MM-DD');
                window.open('/relatorio/jogadores/atividade/xlsx/'+dataInicial+'/'+dataFinal, '_blank');

            }else{
                console.log('q');
            }
        }
    </script>
@stop




