<?php

class AuthenticateController extends BaseController {

	public function authenticate()
	{
		if (Auth::once(Input::only('email', 'password'), false)) {
			return $this->response(Auth::user());
		}

		return $this->notValidResponse('Invalid credentials provided.', 401);
	}

}
