<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsuariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('usuarios', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('nome', 100)->nullable();
			$table->string('apelido', 100)->nullable();
			$table->string('password', 100)->nullable();
			$table->string('sexo', 100)->nullable();
			$table->integer('pontuacao')->nullable();
			$table->integer('nivel_de_acesso')->nullable();
			$table->integer('campus')->nullable()->index('campus');
			$table->integer('desativado')->nullable();
			$table->integer('banido')->nullable();
			$table->string('remember_token', 100)->nullable();
			$table->string('token', 100)->nullable();
			$table->string('prontuario', 20)->nullable();
			$table->string('status', 100)->nullable();
			$table->string('email', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('usuarios');
	}

}
