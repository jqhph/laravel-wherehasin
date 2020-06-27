<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'test_images';

    /**
     * 获取拥有此图片的模型.
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
