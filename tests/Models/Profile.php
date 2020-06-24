<?php

namespace Dcat\Laravel\Database\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'test_user_profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'postcode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
