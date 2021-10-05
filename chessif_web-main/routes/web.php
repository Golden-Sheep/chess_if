<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('', 'AuthController@getViewLogin')->name('login');
Route::post('login', 'AuthController@postLogin');
Route::get('registro', 'AuthController@getViewRegistro');
Route::post('registro', 'AuthController@postRegistro');

Route::group(['middleware' => ['auth']], function() {

    Route::post('logout', 'AuthController@postLogout')->name('logout');
    Route::get('meusavisos', 'AvisoController@getViewMeusAvisos');
    Route::get('perfil/{id}', 'UsuarioController@getPerfil');

//Rotas Jogador
Route::group(['middleware' => 'checkJogador'], function() {
        Route::get('home', 'HomeController@getHome')->name('home');
        Route::get('partida/{id}', 'JogoController@getRedirecionarModo');
        Route::get('partida/ranqueada/{id}', 'JogoController@getViewJogo');
        Route::get('partida/casual/{id}', 'JogoController@getViewJogo');
        Route::get('partida/replay/{id}', 'JogoController@getViewReplay');
        Route::get('ranking', 'UsuarioController@getViewRanking');
        Route::get('meuperfil', 'UsuarioController@getMeuPefil');
        Route::post('editar/perfil', 'UsuarioController@postEditarPerfil');
});
//Fim Rota Jogador

//Rota Adm
Route::group(['middleware' => 'checkAdm'], function() {


    Route::get('paineldecontrole', 'HomeController@getViewPainelDeControle');

    Route::get('usuarios', 'UsuarioController@getViewUsuarios');
    Route::get('usuarios/cadastrar', 'UsuarioController@getViewCadastro');
    Route::post('usuarios/cadastrar', 'UsuarioController@postCadastrarUsuario');
    Route::get('usuarios/editar/{id}', 'UsuarioController@getViewEditar');
    Route::post('usuarios/editar', 'UsuarioController@postEditar');
    Route::post('usuarios/bloquear', 'UsuarioController@postBloquearUsuario')->name('bloquearUsuario');
    Route::post('usuarios/desbloquear', 'UsuarioController@postDesbloquearUsuario')->name('desbloquearUsuario');
    Route::get('usuarios/bloqueados', 'UsuarioController@getViewUsuariosBloqueados');


    Route::get('titulos', 'TitulosController@getViewTitulos');
    Route::get('titulos/cadastrar', 'TitulosController@getViewCadastro');
    Route::post('titulos/cadastrar', 'TitulosController@postCadastrar');
    Route::get('titulos/editar/{id}', 'TitulosController@getViewEditar');
    Route::post('titulos/editar', 'TitulosController@postEditar');
    Route::post('titulos/excluir', 'TitulosController@postExcluir');

    Route::get('campus', 'CampusController@getViewCampus');
    Route::get('campus/cadastrar', 'CampusController@getViewCadastro');
    Route::post('campus/cadastrar', 'CampusController@postCadastrar');
    Route::get('campus/editar/{id}', 'CampusController@getViewEditar');
    Route::post('campus/editar', 'CampusController@postEditar');
    Route::post('campus/excluir', 'CampusController@postExcluir');

    Route::get('avisos', 'AvisoController@getViewAviso');
    Route::get('avisos/cadastrar', 'AvisoController@getViewCadastro');
    Route::post('avisos/cadastrar', 'AvisoController@postCadastrar');

    Route::get('gerenciartitulo', 'TitulosController@getViewGerenciarTitulo');
    Route::get('gerenciartitulo/{id}', 'TitulosController@getViewTitulosJogador');
    Route::post('gerenciartitulo/adicionar', 'TitulosController@postAdicionarTituloJogador')->name('adicionarTitulo');
    Route::post('gerenciartitulo/remover', 'TitulosController@postRemoverTituloJogador')->name('removerTitulo');

    Route::get('relatorio/jogadores/winrate', 'RelatorioController@getViewRelatorioWinRate');
    Route::get('relatorio/jogadores/winrate/pdf/{dataInicial}/{dataFinal}', 'RelatorioController@getGerarPdfWinrate');
    Route::get('relatorio/jogadores/winrate/xlsx/{dataInicial}/{dataFinal}', 'RelatorioController@getGerarXlsxWinrate');

    Route::get('relatorio/jogadores/atividade', 'RelatorioController@getViewRelatorioAtividadeJogadores');
    Route::get('relatorio/jogadores/atividade/pdf/{dataInicial}/{dataFinal}', 'RelatorioController@getGerarPdfAtividadeJogadores');
});
//Fim rota adm


});