<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Aviso
 * 
 * @property int $para
 * @property int $de
 * @property string $texto
 * @property int $visualizada
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Usuario $usuario
 *
 * @package App\Models
 */
class Aviso extends Model
{
	use SoftDeletes;
	protected $table = 'avisos';
	public $incrementing = false;

	protected $casts = [
		'para' => 'int',
		'de' => 'int',
		'visualizada' => 'int'
	];

	protected $fillable = [
		'para',
		'de',
		'texto',
		'visualizada'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'de');
	}
}
