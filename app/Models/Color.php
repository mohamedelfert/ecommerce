<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';

    protected $guarded = [];

    protected $casts = ['created_at' => 'datetime:Y-m-d H:m:s', 'updated_at' => 'datetime:Y-m-d H:m:s'];
}
