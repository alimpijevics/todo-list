<?php

class UserTableSeeder extends Seeder {

    public function run()
    {
        User::create(array(
            'full_name' => 'John Doe',
            'email' => 'foo@bar.com',
            'password' =>  Hash::make('asdf'),
            'api_key' => Str::random(16)
        ));
    }

}
