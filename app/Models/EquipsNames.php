<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipsNames extends Model
{
    use HasFactory;

    public $table = 'equips_names';

    protected $connection = "am";

    protected $fillable = [
        'site_name',
        'box_name',
        'box_type',
        'equip_name',
    ];
}
