<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    function analyze()
    {
    	return $this->belongsTo(Analyze::class);
    }
}
