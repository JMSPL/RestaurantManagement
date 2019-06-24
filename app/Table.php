<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;

    protected $table = "restaurant_tables";
    protected $primaryKey = "table_number";
    protected $fillable = ["table_number"];
}
