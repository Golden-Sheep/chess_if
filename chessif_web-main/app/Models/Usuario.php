<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Usuario
 * 
 * @property int $id
 * @property string $nome
 * @property string $apelido
 * @property string $password
 * @property string $sexo
 * @property int $pontuacao
 * @property int $nivel_de_acesso
 * @property int $campus
 * @property int $desativado
 * @property int $banido
 * @property string $remember_token
 * @property string $token
 * @property string $prontuario
 * @property string $status
 * @property string $email
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * 
 * @property Aviso $aviso
 * @property Collection|ListaTitulo[] $lista_titulos
 * @property Collection|Partida[] $partidas
 *
 * @package App\Models
 */
class Usuario extends Authenticatable
{
	use SoftDeletes;
	protected $table = 'usuarios';

    //Nivel de acesso
    //1 = admin
    //2 = jogador

    public function foto_perfil()
    {
        return  'https://picsum.photos/300/300';
    }

	protected $casts = [
		'pontuacao' => 'int',
		'nivel_de_acesso' => 'int',
		'campus' => 'int',
		'desativado' => 'int',
		'banido' => 'int'
	];

	protected $hidden = [
		'password',
		'remember_token',
		'token'
	];

	protected $fillable = [
		'nome',
		'apelido',
		'password',
		'sexo',
		'pontuacao',
		'nivel_de_acesso',
		'campus',
		'desativado',
		'banido',
		'remember_token',
		'token',
		'prontuario',
		'status',
		'email'
	];

	public function campus()
	{
		return $this->belongsTo(Campus::class, 'campus');
	}

	public function aviso()
	{
		return $this->hasOne(Aviso::class, 'para');
	}

	public function lista_titulos()
	{
		return $this->hasMany(ListaTitulo::class, 'usuario');
	}

    public function titulo_em_uso()
    {
        return ListaTitulo::where('usuario', $this->id)->where('ativo', 1)->first();
    }

	public function partidas()
	{
       return $this->hasMany(Partida::class, 'jogador_2')->orWhere('jogador_1', $this->id);;
	}

    public function getAuthPassword()
    {
        return bcrypt($this->password);
    }

    public static function validarSexo($string){
        if($string == "Masculino"){
            return true;
        }
        if($string == "Feminino"){
            return true;
        }
        if($string == "Prefiro não dizer"){
            return true;
        }
        return false;
    }

    public function niveldeacessoToString(){
	    if($this->nivel_de_acesso == 1){
	        return "Administrador";
        }
	        return "Jogador";
    }
}
