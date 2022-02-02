<?php

namespace Database\Seeders;

use App\Models\User;
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
        // \App\Models\User::factory(10)->create();
        User::create([
            'name' => 'Admin Cleandry',
            'email' => 'admin@cleandry.id',
            'password' => bcrypt('rahasiabanget'),
            'phone' => '089530713889',
            'outlet_id' => 1,
            'is_super' => 1
        ]);

        User::create([
            'name' => 'Virgiawan Listiyandi',
            'email' => 'lvirgiawan17@gmail.com',
            'password' => bcrypt('rahasiabanget'),
            'phone' => '0895404922800',
            'outlet_id' => null,
            'is_super' => 0
        ]);
    }
}
