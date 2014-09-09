<?php

/**
 * Unit test for times repo
 *
 * @group unit
 * @group times
 */
class TimesRepositoryTest extends DbTestCase
{

    private $user1;
    private $user1Time;
    private $repo;

    public function setUp()
    {
        parent::setUp();

        $this->createTestUsers();
        $this->createTimes();
        $this->be($this->user1);
        $this->repo = new TimesRepository();
    }

    public function testAll()
    {
        $result = $this->repo->all();
        $this->assertEquals(1, count($result));

        $result = $this->repo->all(array('from' => Carbon\Carbon::now()->yesterday()));
        $this->assertEquals(1, count($result));
        
         $result = $this->repo->all(array('to' => Carbon\Carbon::now()->tomorrow()));
        $this->assertEquals(1, count($result));

        $result = $this->repo->all(array('from' => Carbon\Carbon::now()->addWeek()));
        $this->assertEquals(0, count($result));
    }

    public function testCreate()
    {
        $this->assertTrue($this->repo->create([]) instanceof Illuminate\Validation\Validator);
        $this->assertTrue($this->repo->create(['working_hours' => 8]) instanceof Illuminate\Validation\Validator);

        $this->assertFalse($this->repo->create([
                    'worked_hours' => 8,
                    'date' => Carbon\Carbon::now(),
                    'notes' => 'note'
                ]) instanceof Illuminate\Validation\Validator);
    }

    public function testUpdate()
    {
        $res = $this->repo->update($this->user1Time, []);
        $this->assertTrue($res instanceof Illuminate\Validation\Validator);

        $res = $this->repo->update($this->user1Time, ['date' => 'asd']);
        $this->assertTrue($res instanceof Illuminate\Validation\Validator);

        $res = $this->repo->update($this->user1Time, [
            'worked_hours' => 11,
            'date' => Carbon\Carbon::now(),
            'notes' => 'note updated'
        ]);
        $this->assertFalse($res instanceof Illuminate\Validation\Validator);
    }

    /**
     * Helper methods for creating some times
     */
    private function createTimes()
    {
        $this->user1Time = $this->user1->times()->create([
            'worked_hours' => 8,
            'date' => Carbon\Carbon::now(),
            'notes' => 'note'
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
    }

}
