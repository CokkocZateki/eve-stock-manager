<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 'assets';
    protected $primaryKey = 'typeID';
    public $timestamps = false;
}
