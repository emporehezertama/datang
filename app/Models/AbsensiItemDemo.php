<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiItemDemo extends Model
{
	protected $connection = 'mysqlDemoEmp';
	
    protected $table = 'absensi_item';

    public function user()
    {
    	return $this->hasOne('\App\User', 'id', 'user_id');
    }
}
