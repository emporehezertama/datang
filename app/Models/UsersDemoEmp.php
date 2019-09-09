<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDemoEmp extends Model
{
   	protected $connection = 'mysqlDemoEmp';
   	
    protected $table = 'users';

    /**
     * Absensi Setting
     * @return void
     */
    public function absensiSetting()
    {
    	return $this->hasOne('App\Models\AbsensiSetting', 'id', 'absensi_setting_id');
    }

    /**
     * Get Structure
     * @return object
     */
    public function structure()
    {
        return $this->hasOne('\App\Models\StructureOrganizationCustom', 'id', 'structure_organization_custom_id');
    }
}