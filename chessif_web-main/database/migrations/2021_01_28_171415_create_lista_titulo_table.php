<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateListaTituloTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lista_titulo', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('usuario')->nullable()->index('usuario');
			$table->integer('titulo')->nullable()->index('titulo');
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
		Schema::drop('lista_titulo');
	}

}
