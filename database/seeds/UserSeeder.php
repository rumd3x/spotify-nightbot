<?php

use App\User;
use Illuminate\Database\Seeder;
use App\Repositories\UserRepository;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!UserRepository::findById(1)) {
            UserRepository::insert('Administrator', 'admin', 'admin@admin.com', 'changethis');
        }
    }
}
