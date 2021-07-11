<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'ss_id',
        'ss_href',
        'ss_img',
        'short_description',
        'brand',
        'model',
        'year',
        'engine_size',
        'price',
        'location',
    ];

    protected $guarded = [];
}
