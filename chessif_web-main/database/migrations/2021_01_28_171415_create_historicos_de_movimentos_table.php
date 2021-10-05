<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHistoricosDeMovimentosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('historicos_de_movimentos', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('id_partida')->nullable()->index('id_partida');
			$table->integer('n_movimento')->nullable();
			$table->text('fen', 65535)->nullable();
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
		Schema::drop('historicos_de_movimentos');
	}

}
