<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'test_posts';

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
