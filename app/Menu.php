<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'items';
    protected $fillable = ['name', 'description', 'price', 'photo_url', 'type'];
}
