<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmProduct extends Model
{
    //
    protected $connection = 'mysqlCrm';
    
    protected $table = 'crm_product';
}
