<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use HasFactory;

    protected $connection = "am";

    protected $fillable = [
        'user_id',
        'hosts',
        'services',
        'boxes',
        'equips',
    ];
}
