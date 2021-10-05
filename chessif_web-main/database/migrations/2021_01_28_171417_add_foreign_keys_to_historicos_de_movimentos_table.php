<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToHistoricosDeMovimentosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('historicos_de_movimentos', function(Blueprint $table)
		{
			$table->foreign('id_partida', 'historicos_de_movimentos_ibfk_1')->references('id')->on('partidas')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('historicos_de_movimentos', function(Blueprint $table)
		{
			$table->dropForeign('historicos_de_movimentos_ibfk_1');
		});
	}

}
