<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Partida
 * 
 * @property int $id
 * @property int $jogador_1
 * @property int $jogador_2
 * @property Carbon $fim_do_jogo
 * @property int $pontuacao_jogador_1
 * @property int $pontuacao_jogador_2
 * @property int $pontos_ganho_vencedor
 * @property int $pontos_perdidos_perdedor
 * @property int $ganhador
 * @property int $perdedor
 * @property String $motivo
 * @property Carbon $tempo_jogador_1
 * @property Carbon $tempo_jogador_2
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Usuario $usuario
 * @property Collection|HistoricosDeMovimento[] $historicos_de_movimentos
 *
 * @package App\Models
 */
class Partida extends Model
{
	use SoftDeletes;
	protected $table = 'partidas';

	protected $casts = [
		'jogador_1' => 'int',
		'jogador_2' => 'int',
		'pontuacao_jogador_1' => 'int',
		'pontuacao_jogador_2' => 'int',
		'pontos_ganho_vencedor' => 'int',
		'pontos_perdidos_perdedor' => 'int'
	];

	protected $dates = [
		'fim_do_jogo',
		'tempo_jogador_1',
		'tempo_jogador_2'
	];

	protected $fillable = [
		'jogador_1',
		'jogador_2',
		'fim_do_jogo',
		'pontuacao_jogador_1',
		'pontuacao_jogador_2',
		'pontos_ganho_vencedor',
		'pontos_perdidos_perdedor',
		'tempo_jogador_1',
		'tempo_jogador_2'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'jogador_2');
	}

	public function historicos_de_movimentos()
	{
		return $this->hasMany(HistoricosDeMovimento::class, 'id_partida');
	}

    public function pecabranca()
    {
        return $this->belongsTo(Usuario::class, 'jogador_1');
    }

    public function pecaPreta()
    {
        return $this->belongsTo(Usuario::class, 'jogador_2');
    }

    public function ganhador()
    {
        return $this->belongsTo(Usuario::class, 'ganhador');
    }

    public function perdedor()
    {
        return $this->belongsTo(Usuario::class, 'perdedor');
    }



    public function jogador1($idUser){
        if($idUser == $this->jogador_1){
            return 1;
        }else{
            return 0;
        }
    }

    public function corPeca($idUser){
        if($idUser == $this->jogador_1){
            return 'white';
        }else{
            return 'black';
        }
    }

    public function inicialCorPeca($idUser){
        if($idUser == $this->jogador_1){
            return 'w';
        }else{
            return 'b';
        }
    }

    public function historicos_de_movimento()
    {
        return $this->hasOne(HistoricosDeMovimento::class, 'id_partida');
    }

}
