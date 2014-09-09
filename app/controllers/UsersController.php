<?php

class UsersController extends \BaseController {

	protected $usersRepository;

	public function __construct(UsersRepository $usersRepository)
	{
		$this->usersRepository = $usersRepository;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		return $this->response($this->usersRepository->all());
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$userData = Input::only('full_name', 'email', 'password', 'confirm_password');
		$result = $this->usersRepository->create($userData);

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
		$user = $this->usersRepository->find($id);

		if (!$user) {
			return $this->notValidResponse('User with provided ID does not exist', 404);
		}

		return $this->response($user);
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = $this->usersRepository->find($id);

		if (!$user) {
			return $this->notValidResponse('User with provided ID does not exist', 404);
		}

		$userData = Input::only('full_name', 'email');

		$result = $this->usersRepository->update($user, $userData);

		if ($result instanceof Illuminate\Validation\Validator) {
			return $this->notValidResponse($result->messages());
		}

		return $this->response($user);
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = $this->usersRepository->find($id);

		if ($user) {
			$user->delete();
			return $this->response('User successful deleted');
		}

		return $this->notValidResponse('User with provided ID does not exist', 404);
	}


}
