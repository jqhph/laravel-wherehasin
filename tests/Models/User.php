<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'test_users';

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'test_user_tags', 'user_id', 'tag_id');
    }

    public function painters()
    {
        return $this->belongsToMany(Painter::class, 'test_user_painters', 'user_id', 'painter_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
