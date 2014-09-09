<?php

/**
 * Functional test for times api
 *
 * @group functional
 * @group times
 */
class TimesTest extends DbTestCase
{

    private $user1;
    private $user2;
    private $user1Time;
    private $user2Time;

    public function setUp()
    {
        parent::setUp();

        $this->createTestUsers();
        $this->createTimes();
    }

    /**
     * Testing Unauthorized calls
     */
    public function testUnauthorizedCalls()
    {
        $response = $this->call('GET', '/api/v1/times');
        $this->assertResponseStatus(401);
        $this->assertEquals('Unauthorized', $response->getContent());

        $response = $this->call('POST', '/api/v1/times');
        $this->assertResponseStatus(401);

        $response = $this->call('PUT', '/api/v1/times/1');
        $this->assertResponseStatus(401);

        $response = $this->call('DELETE', '/api/v1/times/1');
        $this->assertResponseStatus(401);
    }

    /**
     * Testing GET /api/v1/times
     */
    public function testGetTimes()
    {
        $response = $this->call('GET', '/api/v1/times', [], [], $this->getUser1ApiKey());

        $this->assertResponseOk();
        $times = json_decode($response->getContent(), true);
        $this->assertEquals(1, count($times));
        $this->assertEquals(8, $times[0]['worked_hours']);

        $response = $this->call('GET', '/api/v1/times', [], [], $this->getUser2ApiKey());
        $this->assertResponseOk();
        $times = json_decode($response->getContent(), true);
        $this->assertEquals(1, count($times));
        $this->assertEquals(7, $times[0]['worked_hours']);
    }

    /**
     * Testing DELETE /api/v1/times/{id}
     */
    public function testDeleteTimes()
    {
        $response = $this->call('DELETE', '/api/v1/times/asdf', [], [], $this->getUser1ApiKey());
        $this->assertResponseStatus(404);

        $response = $this->call('DELETE', '/api/v1/times/' . $this->user1Time->id, [], [], $this->getUser1ApiKey());
        $this->assertResponseOk();

        $this->assertEquals(0, $this->user1->times->count());
    }

    /**
     * Testing GET /api/v1/times/{id}
     */
    public function testShowTime()
    {
        $response = $this->call('GET', '/api/v1/times/asdf', [], [], $this->getUser1ApiKey());
        $this->assertResponseStatus(404);

        $response = $this->call('GET', '/api/v1/times/' . $this->user1Time->id, [], [], $this->getUser1ApiKey());
        $this->assertResponseOk();
        $times = json_decode($response->getContent(), true);
        $this->assertEquals('8', $times['worked_hours']);
    }

    /**
     * Testing invalid try to create new time
     */
    public function testPostInvalidTime()
    {
        $response = $this->call('POST', '/api/v1/times', [], [], $this->getUser1ApiKey());
        $this->assertResponseStatus(400);
        $err = json_decode($response->getContent(), true);
        $this->assertEquals(['worked_hours', 'date'], array_keys($err));

        $response = $this->call('POST', '/api/v1/times', ['date' => 'asdf'], [], $this->getUser1ApiKey());
        $this->assertResponseStatus(400);
        $err = json_decode($response->getContent(), true);
        
        $this->assertEquals('The date is not a valid date.', $err['date'][0]);
    }

    /**
     * Testing POST /api/v1/times
     */
    public function testPostValidTime()
    {
        $timeData = [
            'worked_hours' => '9',
            'date' => Carbon\Carbon::now(),
            'notes' => '...'
        ];
        $response = $this->call('POST', '/api/v1/times', $timeData, [], $this->getUser1ApiKey());
        $this->assertResponseOk();
        $times = json_decode($response->getContent(), true);
        $this->assertEquals($times['worked_hours'], 9);
    }

    /**
     * Testing PUT /api/v1/times/{id}
     */
    public function testPutTimes()
    {
        $timeData = [
            'worked_hours' => '9',
            'date' => Carbon\Carbon::now(),
            'notes' => '...'
        ];
        $response = $this->call('PUT', '/api/v1/times/' . $this->user1Time->id, $timeData, [], $this->getUser2ApiKey());
        $this->assertResponseStatus(404);

        $timeData = [
            'worked_hours' => '9',
            'date' => Carbon\Carbon::now(),
            'notes' => '...'
        ];
        $response = $this->call('PUT', '/api/v1/times/' . $this->user1Time->id, $timeData, [], $this->getUser1ApiKey());
        $this->assertResponseOk();
        $time = json_decode($response->getContent(), true);
        $this->assertEquals(9, $time['worked_hours']);
    }

    /**
     * Testing PUT with invalid data
     */
    public function testPutInvalidTime()
    {
        $timeData = [
            'date' => 'fasd',
            'notes' => '....'
        ];
        $response = $this->call('PUT', '/api/v1/times/' . $this->user1Time->id, $timeData, [], $this->getUser1ApiKey());
        $this->assertResponseStatus(400);
        $err = json_decode($response->getContent(), true);
        $this->assertEquals(['worked_hours', 'date'], array_keys($err));
    }

    /**
     * @dataProvider getFilterTimesData
     */
    public function testFilterTimes($query, $expectedCount)
    {
        $response = $this->call('GET', '/api/v1/times', $query, [], $this->getUser1ApiKey());
        $this->assertResponseOk();
        $times = json_decode($response->getContent(), true);
        $this->assertEquals($expectedCount, count($times));
    }

    private function getUser1ApiKey()
    {
        return ['HTTP_X-ApiKey' => $this->user1['api_key']];
    }

    private function getUser2ApiKey()
    {
        return ['HTTP_X-ApiKey' => $this->user2['api_key']];
    }

    /**
     * Helper methods for creating some times
     */
    private function createTimes()
    {
        $this->user1Time = $this->user1->times()->create([
            'worked_hours' => '8',
            'date' => Carbon\Carbon::now(),
            'note' => 'note test'
        ]);

        $this->user2Time = $this->user2->times()->create([
            'worked_hours' => '7',
            'date' => Carbon\Carbon::now(),
            'note' => 'note test 123'
        ]);
    }

    /**
     * Helper method that creates dummy users
     */
    private function createTestUsers()
    {
        $this->user1 = User::create([
                    'full_name' => 'John Doe',
                    'email' => 'John@Doe.com',
                    'password' => Hash::make('John Doe'),
                    'api_key' => Str::random(16)
        ]);

        $this->user2 = User::create([
                    'full_name' => 'Jack Daniels',
                    'email' => 'jack@d.com',
                    'password' => Hash::make('jack'),
                    'api_key' => Str::random(16)
        ]);
    }

    public function getFilterTimesData()
    {
        $provider = [];

        $provider[] = [
            ['from' => Carbon\Carbon::now()->subDay()->toDateString()],
            1
        ];
        $provider[] = [
            ['to' => Carbon\Carbon::now()->addWeek()->toDateString()],
            1
        ];

        return $provider;
    }

}
