<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyze extends Model
{
    function files()
    {
    	return $this->hasMany(File::class);
    }
}
