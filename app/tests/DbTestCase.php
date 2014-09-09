<?php

class DbTestCase extends TestCase {

	public function setUp()
	{
		parent::setUp();

                Route::enableFilters();

		DB::beginTransaction();
	}

	public function tearDown()
	{
		DB::rollback();
	}

}
