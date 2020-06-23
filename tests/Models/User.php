<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'test_users';

    protected $appends = ['full_name', 'position'];

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'test_user_tags', 'user_id', 'tag_id');
    }
}
