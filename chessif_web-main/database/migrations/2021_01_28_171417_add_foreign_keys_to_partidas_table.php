<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPartidasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('partidas', function(Blueprint $table)
		{
			$table->foreign('jogador_1', 'partidas_ibfk_1')->references('id')->on('usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('jogador_2', 'partidas_ibfk_2')->references('id')->on('usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('partidas', function(Blueprint $table)
		{
			$table->dropForeign('partidas_ibfk_1');
			$table->dropForeign('partidas_ibfk_2');
		});
	}

}
