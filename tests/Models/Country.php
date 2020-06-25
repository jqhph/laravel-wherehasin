<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'test_countries';

    public function posts()
    {
        return $this->hasManyThrough(Post::class, User::class);
    }
}
