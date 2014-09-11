<?php

/**
 * Functional test for users api
 *
 * @group functional
 * @group users
 */
class UsersTest extends DbTestCase
{

    /**
     * Testing GET /api/v1/users
     */
    public function testGetUsers()
    {
        $response = $this->call('GET', '/api/v1/users');

        $this->assertResponseOk();
        $this->assertEquals('[]', $response->getContent());

        $this->createTestUser();

        $response = $this->call('GET', '/api/v1/users');

        $this->assertResponseOk();
        $this->assertEquals(1, count(json_decode($response->getContent(), true)));
    }

    /**
     * Testing invalid POST users data
     *
     * @dataProvider getInvalidUsersData
     */
    public function testPostUsersInvalid($userData, $expectedResult)
    {
        $response = $this->call('POST', '/api/v1/users', $userData);

        $this->assertResponseStatus(400);
        $this->assertEquals(json_encode($expectedResult), $response->getContent());
    }

    /**
     * Testing valid POST users data
     */
    public function testPostUsersValid()
    {
        $userData = [
            'full_name' => 'John Doe',
            'email' => 'John@Doe.com',
            'password' => 'John Doe',
            'confirm_password' => 'John Doe',
            'preferred_working_hours' => 8
        ];
        $response = $this->call('POST', '/api/v1/users', $userData);
        $newUser = json_decode($response->getContent(), true);

        $this->assertResponseOk();
        $this->assertEquals($userData['full_name'], $newUser['full_name']);
        $this->assertNotEmpty($newUser['api_key']);
    }

    /**
     * Testing GET /api/v1/users/{id}
     */
    public function testGetUserDetails()
    {
        $response = $this->call('GET', '/api/v1/users/1');
        $this->assertResponseStatus(404);

        $user = $this->createTestUser();

        $response = $this->call('GET', '/api/v1/users/' . $user->id);
        $this->assertResponseOk();
        $responseUser = json_decode($response->getContent(), true);
        $this->assertEquals($user->full_name, $responseUser['full_name']);
    }

    /**
     * Test DELETE /api/v1/users/{id}
     */
    public function testDeleteUser()
    {
        $response = $this->call('DELETE', '/api/v1/users/1');
        $this->assertResponseStatus(404);

        $user = $this->createTestUser();

        $response = $this->call('DELETE', '/api/v1/users/' . $user->id);
        $this->assertResponseOk();

        $response = $this->call('GET', '/api/v1/users');

        $this->assertResponseOk();
        $this->assertEquals('[]', $response->getContent());
    }

    /**
     * Test PUT /api/v1/users/{id}
     */
    public function testValidUpdateUser()
    {
        $response = $this->call('PUT', '/api/v1/users/1');
        $this->assertResponseStatus(404);

        $user = $this->createTestUser();

        $userData = [
            'preferred_working_hours' => '10'
        ];

        $response = $this->call('PUT', '/api/v1/users/' . $user->id, $userData);
        $this->assertResponseOk();
        $responseUser = json_decode($response->getContent(), true);
        $this->assertEquals($userData['preferred_working_hours'], $responseUser['preferred_working_hours']);
    }

    /**
     * Testing invalid PUT users data
     */
    public function testNotValidUpdateUser()
    {
        $user = $this->createTestUser();

        $userData = [
            'preferred_working_hours' => 'test'
        ];

        $response = $this->call('PUT', '/api/v1/users/' . $user->id, $userData);
        $this->assertResponseStatus(400);
        $responseJson = json_decode($response->getContent(), true);
        $this->assertEquals(['preferred_working_hours' => ['The preferred working hours must be a number.']], $responseJson);
    }

    /**
     * Helper method that creates dummy user
     */
    private function createTestUser()
    {
        $userData = [
            'full_name' => 'John Doe',
            'email' => 'John@Doe.com',
            'password' => Hash::make('John Doe'),
            'api_key' => Str::random(16),
            'preferred_working_hours' => 8
        ];

        return User::create($userData);
    }

    /**
     * Data provider for testing create user with invalid data
     */
    public function getInvalidUsersData()
    {
        $provider = [];

        $provider[] = [
            ['full_name' => 'John Doe', 'email' => 'John Doe'],
            ['email' => ['The email must be a valid email address.'], 'password' => ['The password field is required.']]
        ];

        $provider[] = [
            [],
            ["full_name" => ["The full name field is required."], "email" => ["The email field is required."], "password" => ["The password field is required."]]
        ];

        $provider[] = [
            ['full_name' => 'John Doe', 'email' => 'John@Doe.com', 'password' => 'asdf'],
            ['password' => ['The password and confirm password must match.']]
        ];

        $provider[] = [
            ['full_name' => 'John Doe', 'email' => 'John@Doe.com', 'password' => 'asdf', 'confirm_password' => 'aaaaa'],
            ['password' => ['The password and confirm password must match.']]
        ];

        return $provider;
    }

}
