<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DryRun extends Model
{
    use HasFactory;
    protected $fillable = ['plaintext', 'encryption_time', 'cypher', 'tag'];
}
