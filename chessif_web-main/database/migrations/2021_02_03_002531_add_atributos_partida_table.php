<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAtributosPartidaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->integer('ganhador')->nullable()->default(null);
            $table->integer('perdedor')->nullable()->default(null);
            $table->text('motivo')->nullable()->default(null);

            $table->foreign('perdedor')
                ->references('id')->on('usuarios')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('ganhador')
                ->references('id')->on('usuarios')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn('ganhador');
            $table->dropColumn('perdedor');
            $table->dropColumn('motivo');
        });
    }
}
