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
 * Class Campus
 * 
 * @property int $id
 * @property string $sigla
 * @property string $nome
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Collection|Usuario[] $usuarios
 *
 * @package App\Models
 */
class Campus extends Model
{
	use SoftDeletes;
	protected $table = 'campus';

	protected $fillable = [
		'sigla',
		'nome'
	];

	public function usuarios()
	{
		return $this->hasMany(Usuario::class, 'campus');
	}
}
