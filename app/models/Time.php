<?php

use Carbon\Carbon;

class Time extends Eloquent {

    protected $fillable = array('user_id', 'worked_hours', 'date', 'notes');

    public static $storeRules = array(
        'worked_hours' => 'numeric|required',
        'date' => 'date|required'
    );

    public function user()
    {
        return $this->belongsTo('User');
    }
}