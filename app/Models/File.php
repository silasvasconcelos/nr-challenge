<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
	protected $fillable = [
		'name',
		'file',
	];
	
    function analyze()
    {
    	return $this->belongsTo(Analyze::class);
    }
}
