<?php

use Carbon\Carbon;

class TimeTableSeeder extends Seeder {

    public function run()
    {
        Time::create(array(
            'user_id' => 1,
            'worked_hours' => 8,
            'date' => Carbon::now()->addDay(),
            'notes' => 'This is note...'
        ));
    }
}