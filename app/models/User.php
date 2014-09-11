<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface
{

    use UserTrait,
        RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';
    protected $fillable = array('full_name', 'email', 'password', 'api_key', 'preferred_working_hours');

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');
    public static $storeRules = array(
        'full_name' => 'required',
        'email' => 'unique:users|required|email',
        'password' => 'required|same:confirm_password',
        'preferred_working_hours' => 'numeric'
    );
    public static $updateRules = array(
        'preferred_working_hours' => 'numeric'
    );

    public function times()
    {
        return $this->hasMany('Time');
    }

}
