<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipsDetail extends Model
{
    use HasFactory;

    public $table = 'equips_details';

    protected $connection = "am";

    protected $fillable = [
        'site_name',
        'box_name',
        'box_type',
        'equip_name',
        'pin_name',
        'working_state',
        'hall_name',       
    ];
}
