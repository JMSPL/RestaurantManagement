<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    public function item(){
        return $this->hasOne('App\Menu', 'id', 'item_id')->withTrashed();
    }
}
