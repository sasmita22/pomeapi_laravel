<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectStructureStuffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_structure_staff', function (Blueprint $table) {
            $table->integer('id_project_structure')->unsigned();
            $table->string('staff',10);
            $table->primary(array('id_project_structure','staff'));
            $table->tinyInteger('leader');
            $table->foreign('id_project_structure')->references('id')->on('project_structures');
            $table->foreign('staff')->references('nip')->on('staff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_structure_staff');
    }
}
