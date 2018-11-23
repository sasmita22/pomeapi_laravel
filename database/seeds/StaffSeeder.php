<?php

use Illuminate\Database\Seeder;
use App\Staff;
use Faker\Factory as Faker;

class StaffSeeder extends Seeder
{
	protected $staff;
	protected $faker;


	public function __construct(Staff $staff, Faker $faker)
	{
		$this->staff = $staff;
		$this->faker = $faker;
	}
    public function run()
    {
        
        foreach (range(1,10) as $x) {
        	$this->staff->create([
        	'nip' => $this->faker->randomDigit,
        	'name' => $this->faker->name(10),
        	'email' => $this->faker->unique()->safeEmail,
        	'password' => str_random(10),
        	]);
        }

        // Staff::create([
        // 	'nip' => '1234567890',
        // 	'name' => 'Gumilar Fajar Darajat',
        // 	'email' => 'gumilarfajardarajat@gmail.com',
        // 	'password' => 'TakiyaGenjeh'
        // ]);

    }
}
