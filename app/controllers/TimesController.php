<?php

class TimesController extends \BaseController
{

    protected $timesRepository;

    public function __construct(TimesRepository $timesRepository)
    {
        $this->timesRepository = $timesRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $q = Input::only('from', 'to');

        return $this->response($this->timesRepository->all($q));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $timeData = Input::only('worked_hours', 'date', 'notes');

        $result = $this->timesRepository->create($timeData);

        if ($result instanceof Illuminate\Validation\Validator) {
            return $this->notValidResponse($result->messages());
        }

        return $this->response($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $time = $this->timesRepository->find($id);

        if (!$time) {
            return $this->notValidResponse('Time with provided ID does not exist', 404);
        }

        return $this->response($time);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $time = $this->timesRepository->find($id);

        if (!$time) {
            return $this->notValidResponse('Time with provided ID does not exist', 404);
        }

        $timeData = Input::only('worked_hours', 'date', 'notes');

        $result = $this->timesRepository->update($time, $timeData);

        if ($result instanceof Illuminate\Validation\Validator) {
            return $this->notValidResponse($result->messages());
        }

        return $this->response($time);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $time = $this->timesRepository->find($id);

        if ($time) {
            $time->delete();
            return $this->response('Time successful deleted');
        }

        return $this->notValidResponse('Time with provided ID does not exist', 404);
    }

}
