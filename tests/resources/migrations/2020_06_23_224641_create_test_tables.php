<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('supplier_id')->default(0);
            $table->integer('country_id')->default(0);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('test_user_profiles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('postcode')->nullable();
            $table->timestamps();
        });

        Schema::create('test_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('test_user_tags', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('tag_id');
            $table->index(['user_id', 'tag_id']);
            $table->timestamps();
        });

        Schema::create('test_user_painters', function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('painter_id');
            $table->index(['user_id', 'painter_id']);
            $table->timestamps();
        });

        Schema::create('test_painters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->default('');
            $table->timestamps();
        });

        Schema::create('test_paintings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('painter_id')->default('');
            $table->string('title')->default('');
            $table->string('body')->nullable();
            $table->timestamps();
        });

        Schema::create('test_suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->timestamps();
        });

        Schema::create('test_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('log')->default('');
            $table->timestamps();
        });

        Schema::create('test_countries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('');
            $table->timestamps();
        });
        Schema::create('test_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->default(0);
            $table->string('title')->default('');
            $table->timestamps();
        });

        Schema::create('test_images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url')->nullable();
            $table->integer('imageable_id')->default(0);
            $table->string('imageable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('test_users');
        Schema::dropIfExists('test_user_profiles');
        Schema::dropIfExists('test_tags');
        Schema::dropIfExists('test_user_tags');
        Schema::dropIfExists('test_user_painters');
        Schema::dropIfExists('test_painters');
        Schema::dropIfExists('test_paintings');
        Schema::dropIfExists('test_suppliers');
        Schema::dropIfExists('test_histories');
        Schema::dropIfExists('test_countries');
        Schema::dropIfExists('test_posts');
        Schema::dropIfExists('test_images');
    }
}
