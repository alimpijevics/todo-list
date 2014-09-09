<?php

class UsersRepository {

    public function create($userData)
    {
        $validator = Validator::make($userData, User::$storeRules);

        if ($validator->fails()) {
            return $validator;
        }

        $userData['password'] = Hash::make($userData['password']);
        $userData['api_key'] = Str::random(16);

        return User::create($userData);
    }

    public function all()
    {
        return User::all();
    }

    public function find($id)
    {
        return User::find($id);
    }

    public function update(&$user, $userData)
    {
        $validator = Validator::make($userData, User::$updateRules);

        if ($validator->fails()) {
            return $validator;
        }

        return $user->update($userData);
    }

}
