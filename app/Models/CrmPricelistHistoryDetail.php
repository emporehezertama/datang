<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmPricelistHistoryDetail extends Model
{
    //
    protected $connection = 'mysqlCrm';
    
    protected $table = 'crm_pricelist_history_detail';
}
