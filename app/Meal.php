<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    protected $table = 'meals';
    protected $fillable = ['table_number', 'responsible_waiter_id', 'start'];

    public function table(){
        return $this->hasOne('App\Table', 'table_number', 'table_number');
    }

    public function waiter(){
        return $this->hasOne('App\Worker', 'id', 'responsible_waiter_id');
    }

    public function orders(){
        return $this->hasMany('App\Order', 'meal_id', 'id');
    }
}
