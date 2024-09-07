<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sensor_reading extends Model
{
    use HasFactory;

    protected $fillable=[
        'sensor_id','encrypted_reading','tag'
    ];
}
