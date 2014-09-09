<?php

use Carbon\Carbon;

class Time extends Eloquent {

    protected $fillable = array('user_id', 'worked_hours', 'date', 'notes');

    public static $storeRules = array(
        'worked_hours' => 'required',
        'date' => 'date|required'
    );

    public function user()
    {
        return $this->belongsTo('User');
    }

    public function getDates()
    {
        return array('date', 'created_at', 'updated_at');
    }

}