<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAvisosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('avisos', function(Blueprint $table)
		{
			$table->foreign('para', 'avisos_ibfk_1')->references('id')->on('usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
			$table->foreign('de', 'avisos_ibfk_2')->references('id')->on('usuarios')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('avisos', function(Blueprint $table)
		{
			$table->dropForeign('avisos_ibfk_1');
			$table->dropForeign('avisos_ibfk_2');
		});
	}

}
