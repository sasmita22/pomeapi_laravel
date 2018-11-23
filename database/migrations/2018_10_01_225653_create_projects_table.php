<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id_project');
            $table->string('name',50);
            $table->string('project_manager',10);
            $table->timestamp('start_at')->nullable()->default(null);
            $table->timestamp('ended_at')->nullable()->default(null);
            $table->timestamp('deadline_at')->nullable()->default(null);
            $table->integer('status');
            $table->bigInteger('price');
            $table->foreign('project_manager')->references('nip')->on('staff')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
