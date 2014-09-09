<?php

class TimesRepository {

    public function all($query = array())
    {
        $q = Auth::user()->times();

        if (!empty($query['from'])) {
            $q->where('date', '>=', $query['from']);
        }
        if (!empty($query['to'])) {
            $q->where('date', '<=', $query['to']);
        }

        return $q->get();
    }

    public function create($timeData)
    {
        $validator = Validator::make($timeData, Time::$storeRules);

        if ($validator->fails()) {
            return $validator;
        }

        return Auth::user()->times()->create($timeData);
    }

    public function find($id)
    {
        return Auth::user()->times()->find($id);
    }

    public function update(&$time, $timeData)
    {
        $validator = Validator::make($timeData, Time::$storeRules);

        if ($validator->fails()) {
            return $validator;
        }

        return $time->update($timeData);
    }
}
