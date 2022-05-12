<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sites extends Model
{
    use HasFactory;

    public $table = 'all_sites';

    protected $connection = "am";

    protected $fillable = [
        'user_id',
        'site_name',
    ];
}
