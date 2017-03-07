<?php

require '../vendor/autoload.php';
require 'database.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->dropIfExists('downloads');
Capsule::schema()->dropIfExists('upload_files');
Capsule::schema()->dropIfExists('upload_groups');

Capsule::schema()->create('upload_groups', function($table)
{
	$table->increments('id');
	$table->string('download_uid', 32)->unique();
	// $table->timestamp('upload_date');
	$table->dateTime('expiration_date');
	$table->string('password')->nullable();
	$table->boolean('validity')->default(true);
	$table->timestamps();
});

Capsule::schema()->create('upload_files', function($table)
{
	$table->increments('id');
	$table->integer('upload_group_id')->unsigned();
	$table->foreign('upload_group_id')->references('id')->on('upload_groups')->onDelete('cascade');
	$table->text('file_name');
	$table->text('file_directory');
	$table->integer('file_size');
	$table->boolean('validity')->default(true);
	$table->timestamps();
});

Capsule::schema()->create('downloads', function($table)
{
	$table->increments('id');
	$table->integer('upload_file_id')->unsigned();
	$table->foreign('upload_file_id')->references('id')->on('upload_files')->onDelete('cascade');
	$table->timestamp('download_date');
	$table->string('ip_address');
	$table->string('host');
	$table->string('user_agent');
	$table->timestamps();
});

echo 'Migrations complete.';