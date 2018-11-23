<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUpdateProjectStructuresTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER update_project_structures AFTER UPDATE ON tasks
            FOR EACH ROW
            BEGIN
                SET @id = new.project_structure;
               
                set @beres = (SELECT COUNT(IF(status=0,1,null)) = 0
                from tasks
                where project_structure = @id
                group by project_structure);       
                
                IF @beres = 1 then
                    BEGIN
                        update project_structures
                        set status = 1
                        where id = @id;            
                    END;
                ELSE
                    BEGIN
                        update project_structures
                        set status = 0
                        where id = @id;               
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
        DB::unprepared('DROP TRIGGER update_project_structures');
    }
}
