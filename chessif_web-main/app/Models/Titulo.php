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
 * Class Titulo
 * 
 * @property int $id
 * @property string $descricao
 * @property string $nome
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Collection|ListaTitulo[] $lista_titulos
 *
 * @package App\Models
 */
class Titulo extends Model
{
	use SoftDeletes;
	protected $table = 'titulos';

	protected $fillable = [
		'descricao',
		'nome'
	];

	public function lista_titulos()
	{
		return $this->hasMany(ListaTitulo::class, 'titulo');
	}
}
