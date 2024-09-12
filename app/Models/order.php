<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $fillable =[
        "id","clien_p_nb","plate_nb","worker_p_nb","city"
    ];
}