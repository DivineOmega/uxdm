<?php

namespace DivineOmega\uxdm\TestClasses\Eloquent;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public $primaryKey = 'id';
    public $timestamps = false;
}
