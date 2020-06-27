<?php

use Dcat\Laravel\Database\Tests\Models;
use Faker\Factory as FakerFactory;

function create_suppliers()
{
    $max = 200;

    $factory = FakerFactory::create();

    $data = [];

    for ($i = 0; $i < $max; $i++) {
        $data[] = [
            'id'   => $i + 1,
            'name' => $factory->name,
        ];
    }

    if (! Models\Supplier::count()) {
        Models\Supplier::insert($data);
    }

    return $data;
}

function create_contries()
{
    $factory = FakerFactory::create();
    $max = 200;

    $data = [];

    for ($i = 0; $i < $max; $i++) {
        $data[] = [
            'id'   => $i + 1,
            'name' => $factory->name,
        ];
    }

    if (! Models\Country::count()) {
        Models\Country::insert($data);
    }

    return $data;
}

function create_posts()
{
    $factory = FakerFactory::create();
    $max = 200;

    $data = [];

    for ($i = 0; $i < $max; $i++) {
        $data[] = [
            'id'      => $i + 1,
            'user_id' => mt_rand(1, 200),
            'title'   => $factory->title,
        ];
    }

    if (! Models\Post::count()) {
        Models\Post::insert($data);
    }

    return $data;
}

function create_users()
{
    $factory = FakerFactory::create();
    $max = 200;

    $data = [];

    for ($i = 0; $i < 200; $i++) {
        $data[] = [
            'id'                => $i + 1,
            'name'              => $factory->name,
            'email'             => $factory->email,
            'supplier_id'       => mt_rand(1, $max),
            'country_id'        => mt_rand(1, $max),
            'password' => $factory->password,
        ];
    }

    if (! Models\User::count()) {
        Models\User::insert($data);

        collect($data)->map(function ($data) {
            $user = new Models\User();

            $user->forceFill($data);

            $user->exists = true;

            return $user;
        })->each(function (Models\User $user) {
            $user->profile()->save(make_profile());
            $user->tags()->saveMany(make_tags());

            $user->painters()->saveMany($painters = make_painters());

            $painters->each(function (Models\Painter $painter) {
                $painter->paintings()->saveMany($painters = make_paintings());
            });
        });
    }

    return $data;
}

function create_histories()
{
    $factory = FakerFactory::create();

    for ($i = 0; $i < 200; $i++) {
        $data[] = [
            'id'      => $i + 1,
            'user_id' => mt_rand(1, 200),
            'log'     => $factory->text(40),
        ];
    }

    if (! Models\History::count()) {
        Models\History::insert($data);
    }

    return $data;
}

function make_profile()
{
    $factory = FakerFactory::create();

    return new Models\Profile([
        'first_name' => $factory->firstName,
        'last_name'  => $factory->lastName,
        'postcode'   => $factory->postcode,
    ]);
}

function make_tags()
{
    $factory = FakerFactory::create();

    $data = [];
    for ($i = 0; $i < mt_rand(1, 5); $i++) {
        $data[] = new Models\Tag([
            'name' => $factory->word,
        ]);
    }

    return $data;
}

function make_painters()
{
    static $id = 0;

    $factory = FakerFactory::create();

    $data = [];
    for ($i = 0; $i < mt_rand(1, 5); $i++) {
        $id++;

        $data[] = new Models\Painter([
            'id'       => $id,
            'username' => $factory->userName,
        ]);
    }

    return collect($data);
}

function make_paintings()
{
    $factory = FakerFactory::create();

    $data = [];
    for ($i = 0; $i < mt_rand(1, 5); $i++) {
        $data[] = new Models\Painting([
            'title' => $factory->title,
            'body'  => $factory->text(20),
        ]);
    }

    return $data;
}
