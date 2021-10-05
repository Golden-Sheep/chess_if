<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToListaTituloTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('lista_titulo', function(Blueprint $table)
		{
			$table->foreign('usuario', 'lista_titulo_ibfk_1')->references('id')->on('usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('titulo', 'lista_titulo_ibfk_2')->references('id')->on('titulos')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('lista_titulo', function(Blueprint $table)
		{
			$table->dropForeign('lista_titulo_ibfk_1');
			$table->dropForeign('lista_titulo_ibfk_2');
		});
	}

}
