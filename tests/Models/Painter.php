<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Painter extends Model
{
    protected $table = 'test_painters';

    protected $fillable = ['username', 'bio', 'id'];

    public function paintings()
    {
        return $this->hasMany(Painting::class, 'painter_id');
    }
}
