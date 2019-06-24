<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = ['name', 'username', 'type', 'email','password','photo_url', 'email_verified_at','last_shift_end','last_shift_start', 'shift_active', 'intern'];
    protected $table="users";
    protected $hidden = ['password'];
}
