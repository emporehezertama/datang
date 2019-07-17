<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDemoEmp extends Model
{
   	protected $connection = 'mysqlDemoEmp';
   	
    protected $table = 'users';
}
