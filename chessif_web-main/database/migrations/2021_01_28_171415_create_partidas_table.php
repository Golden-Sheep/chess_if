<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePartidasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('partidas', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('jogador_1')->nullable()->index('jogador_1');
			$table->integer('jogador_2')->nullable()->index('jogador_2');
			$table->dateTime('fim_do_jogo')->nullable();
			$table->integer('pontuacao_jogador_1')->nullable();
			$table->integer('pontuacao_jogador_2')->nullable();
			$table->integer('pontos_ganho_vencedor')->nullable();
			$table->integer('pontos_perdidos_perdedor')->nullable();
			$table->time('tempo_jogador_1')->nullable();
			$table->time('tempo_jogador_2')->nullable();
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
		Schema::drop('partidas');
	}

}
