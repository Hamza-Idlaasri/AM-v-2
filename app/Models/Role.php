<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    protected $connection = "am";

    public $guarded = [];
}
