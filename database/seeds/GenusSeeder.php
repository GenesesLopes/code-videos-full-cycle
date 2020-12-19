<?php

use App\Models\Genus;
use Illuminate\Database\Seeder;

class GenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Genus::class,100)->create();
    }
}
