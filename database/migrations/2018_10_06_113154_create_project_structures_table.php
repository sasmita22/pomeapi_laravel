<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_structures', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_project')->unsigned();
            $table->integer('step')->unsigned();
            $table->unique(array('id_project','step'));
            $table->integer('status');
            $table->foreign('step')->references('id')->on('steps');
            $table->foreign('id_project')->references('id_project')->on('projects')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_structures');
    }
}
