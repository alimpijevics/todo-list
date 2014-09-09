<?php

class BaseController extends Controller
{

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function response($data, $status = 200)
    {
        if (is_string($data)) {
            $data = array('msg' => $data);
        }

        return Response::json($data, $status);
    }

    protected function notValidResponse($data, $status = 400)
    {
        return $this->response($data, $status);
    }

}
