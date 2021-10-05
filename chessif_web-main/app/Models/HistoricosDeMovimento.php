<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class HistoricosDeMovimento
 * 
 * @property int $id
 * @property int $id_partida
 * @property int $n_movimento
 * @property string $fen
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Partida $partida
 *
 * @package App\Models
 */
class HistoricosDeMovimento extends Model
{
	use SoftDeletes;
	protected $table = 'historicos_de_movimentos';

	protected $casts = [
		'id_partida' => 'int',
		'n_movimento' => 'int'
	];

	protected $fillable = [
		'id_partida',
		'n_movimento',
		'fen'
	];

	public function partida()
	{
		return $this->belongsTo(Partida::class, 'id_partida');
	}
}
