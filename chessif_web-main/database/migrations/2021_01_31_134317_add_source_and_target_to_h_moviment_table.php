<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceAndTargetToHMovimentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historicos_de_movimentos', function (Blueprint $table) {
            $table->string('source', 20)->nullable();
            $table->string('target', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historicos_de_movimentos', function (Blueprint $table) {
            $table->dropColumn('source');
            $table->dropColumn('target');
        });
    }
}
