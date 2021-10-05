<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAvisosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('avisos', function(Blueprint $table)
		{
			$table->integer('para')->nullable()->index('para');
			$table->integer('de')->nullable()->index('de');
			$table->text('texto', 65535)->nullable();
			$table->integer('visualizada')->nullable();
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
		Schema::drop('avisos');
	}

}
