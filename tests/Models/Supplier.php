<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'test_suppliers';

    /**
     * 用户的历史记录.
     */
    public function userHistory()
    {
        return $this->hasOneThrough(History::class, User::class);
    }
}
