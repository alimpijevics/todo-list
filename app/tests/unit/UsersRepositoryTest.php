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
			'confirm_password' => 'asdf',
		]);

		$res = $this->repo->update($newUser, [
			'preferred_working_hours' => '8'
		]);
		$this->assertFalse($res instanceof Illuminate\Validation\Validator);
		$this->assertEquals(8, $newUser->preferred_working_hours);
	}


}
