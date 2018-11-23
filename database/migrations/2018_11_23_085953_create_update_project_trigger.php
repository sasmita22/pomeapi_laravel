<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateProjectTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_project AFTER UPDATE ON project_structures
            FOR EACH ROW
            BEGIN
                SET @id = new.id_project;
            
                set @beres = (SELECT COUNT(IF(status=0,1,null)) = 0
                from project_structures
                where id_project = @id
                group by id_project);       
                
                IF @beres = 1 then
                    BEGIN
                        update projects
                        set status = 1
                        where id_project = @id;            
                    END;
                ELSE
                    BEGIN
                        update projects
                        set status = 0
                        where id_project = @id;               
                    END;
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER update_project');
    }
}
