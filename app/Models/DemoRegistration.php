<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoRegistration extends Model
{
    protected $fillable = [
        'username',
        'email',
        'phone',
        'site_name',
    ];
}
