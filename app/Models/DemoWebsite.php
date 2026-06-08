<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoWebsite extends Model
{
    protected $fillable = [
        'name',
        'url',
        'icon_url',
    ];
}
