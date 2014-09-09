<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTimeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
            Schema::create('times', function($table) {
	        $table->increments('id');
	        $table->integer('user_id')->unsigned();
	        $table->float('worked_hours');
	        $table->date('date');
	        $table->text('notes');
	        $table->timestamps();

	        $table->foreign('user_id')
	              ->references('id')->on('users')
	              ->onDelete('cascade');
	    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('times');
	}

}
