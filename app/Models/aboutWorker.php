<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class aboutWorker extends Model
{
    use HasFactory;
    protected $fillable =[
        "id","phone","profession","experience","other"
    ];
}