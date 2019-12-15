<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SizeAndQuantity extends Model
{
	protected $table = 'sizeandquantity';

    public function user() {
    	return $this->belongsTo('App\User');
    }
}
