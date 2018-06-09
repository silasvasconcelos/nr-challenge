<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analyze extends Model
{
	protected $fillable = [
		'origin',
		'name',
		'object',
		'starting_date',
		'published',
		'hash'
	];

    function files()
    {
    	return $this->hasMany(File::class);
    }
}
