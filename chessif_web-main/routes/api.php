<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//202 = Token valido, usuário encontrado
//404 = Token invalido, nenhum usuário encontrado
//406 = Token não encontrado
Route::post('/validar/token/usuario', 'UsuarioController@validarTokenUsuario');
// ======

Route::post('/usuario/partida/verificar', 'PartidaController@verificarSeExistePartidaEmAndamento');
Route::post('/criar/partida', 'PartidaController@criarPartida');

Route::post('/finalizar/partida', 'PartidaController@finalizarPartida');

//202 = Token valido, usuário encontrado
//404 = Token invalido, nenhum usuário encontrado
//406 = Token não encontrado
Route::post('/validar/partida/usuario', 'PartidaController@validarPartidaUsuario');
// ======
