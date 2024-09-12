<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarrEliasWorker extends Model
{
    use HasFactory;
    protected $fillable=[
        "id","street","more_details","phone"
    ];
}