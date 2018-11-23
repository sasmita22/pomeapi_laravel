<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('status');
            $table->integer('type')->length(10)->unsigned();
            $table->integer('project_structure')->length(10)->unsigned();
            $table->timestamp('deadline_at')->nullable()->default(null);
            $table->timestamp('finished_at')->nullable()->default(null);
            $table->string('handled_by',10);

            $table->foreign('type')->references('id')->on('types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('handled_by')->references('nip')->on('staff')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('project_structure')->references('id')->on('project_structures')->onDelete('cascade')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
