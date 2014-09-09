<?php

/**
 * Unit test for users repo
 *
 * @group unit
 * @group users
 */
class UsersRepositoryTest extends DbTestCase {

	private $repo;

	public function setUp()
	{
		parent::setUp();

		$this->repo = new UsersRepository();
	}

	public function testCreate()
	{
		$this->assertTrue($this->repo->create([]) instanceof Illuminate\Validation\Validator);
		$this->assertTrue($this->repo->create(['full_name' => 'asdf']) instanceof Illuminate\Validation\Validator);
		$this->assertTrue($this->repo->create([
			'full_name' => 'John Doe',
			'email' => 'John@Doe.com'
		]) instanceof Illuminate\Validation\Validator);

		$this->assertTrue($this->repo->create([
			'full_name' => 'John Doe',
			'email' => 'John@Doe.com',
			'password' => 'asdf',
			'confirm_password' => 'pppppp'
		]) instanceof Illuminate\Validation\Validator);

		$newUser = $this->repo->create([
			'full_name' => 'John Doe',
			'email' => 'John@Doe.com',
			'password' => 'asdf',
			'confirm_password' => 'asdf'
		]);
		$this->assertNotEmpty($newUser->api_key);
	}

	public function testUpdate()
	{
		$newUser = $this->repo->create([
			'full_name' => 'John Doe',
			'email' => 'John@Doe.com',
			'password' => 'asdf',
			'confirm_password' => 'asdf'
		]);

		$res = $this->repo->update($newUser, []);
		$this->assertTrue($res instanceof Illuminate\Validation\Validator);

		$res = $this->repo->update($newUser, ['full_name' => 'asd']);
		$this->assertTrue($res instanceof Illuminate\Validation\Validator);

		$res = $this->repo->update($newUser, [
			'full_name' => 'John Doe',
			'email' => 'test@test.com',
		]);
		$this->assertFalse($res instanceof Illuminate\Validation\Validator);
		$this->assertEquals('test@test.com', $newUser->email);
	}


}
