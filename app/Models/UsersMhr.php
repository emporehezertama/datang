<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersMhr extends Model
{
   	protected $connection = 'mysqlMhr';

    protected $table = 'users';

    /**
     * Absensi Setting
     * @return void
     */
    public function absensiSetting()
    {
    	return $this->hasOne('App\Models\AbsensiSettingMhr', 'id', 'absensi_setting_id');
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
