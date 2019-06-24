<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';

    protected $fillable = [
            'name', 'nif','num_customers',
        ];

    public function items(){
        return $this->belongsToMany('App\Menu','invoice_items', 'invoice_id', 'item_id')
            ->withTrashed()->withPivot('quantity','unit_price','sub_total_price');
    }

    public function meal(){
        return $this->belongsTo('App\Meal','meal_id', 'id');
    }
}
