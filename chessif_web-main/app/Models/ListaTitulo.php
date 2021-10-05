<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ListaTitulo
 * 
 * @property int $id
 * @property int $usuario
 * @property int $titulo
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 *
 * @package App\Models
 */
class ListaTitulo extends Model
{
	use SoftDeletes;
	protected $table = 'lista_titulo';

	protected $casts = [
		'usuario' => 'int',
		'titulo' => 'int'
	];

	protected $fillable = [
		'usuario',
		'titulo'
	];

	public function usuario()
	{
		return $this->belongsTo(Usuario::class, 'usuario');
	}

	public function titulo()
	{
		return $this->belongsTo(Titulo::class, 'titulo');
	}
}
