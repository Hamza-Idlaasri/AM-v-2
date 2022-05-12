<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersSite extends Model
{
    use HasFactory;

    public $table = 'users_sites';

    protected $connection = "am";

    protected $fillable = [
        'user_id',
        'site_name',
        'current_site',
    ];
}
