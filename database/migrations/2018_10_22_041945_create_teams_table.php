<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->integer('id_team')->unsigned();
            $table->string('staff',10);
            $table->primary(array('id_team','staff'));

            $table->foreign('id_team')->references('id')->on('project_structures')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('staff')->references('nip')->on('staff')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teams');
    }
}
