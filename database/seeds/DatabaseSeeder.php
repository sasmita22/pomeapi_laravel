<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(StaffSeeder::class);
        // factory('App\Staff',10)->create();
         factory('App\Project',10)->create();
        //factory('App\Type',10)->create();
        //factory('App\Step',10)->create();
        //factory('App\ProjectStructure',10)->create();
        //factory('App\Task',10)->create();
        //factory('App\Invoice',10)->create();
        //factory('App\Team',10)->create();
        //factory('App\Payment',10)->create();
    }
}
