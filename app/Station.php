<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'staStations';
    protected $primaryKey = 'stationID';
    public $timestamps = false;
}
