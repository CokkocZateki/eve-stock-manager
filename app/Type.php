<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'invTypes';
    protected $primaryKey = 'typeID';
    public $timestamps = false;
}
