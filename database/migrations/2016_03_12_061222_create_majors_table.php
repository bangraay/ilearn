<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('majors', function (Blueprint $table) {
			$table->uuid('id')->unique();
			$table->string('name', 30);
			$table->string('description', 150);
			$table->timestamps();

			$table->primary(['id']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('majors');
	}
}
