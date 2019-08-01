<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiItemMhr extends Model
{
	 protected $connection = 'mysqlMhr';
	
    protected $table = 'absensi_item';

    public function user()
    {
    	return $this->hasOne('\App\User', 'id', 'user_id');
    }
}
