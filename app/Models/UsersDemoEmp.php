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
     * [empore_staff description]
     * @return [type] [description]
     */
    public function empore_staff()
    {
        return $this->hasOne('App\Models\EmporeOrganisasiStaff', 'id', 'empore_organisasi_staff_id');
    }

    /**
     * [empore_staff description]
     * @return [type] [description]
     */
    public function empore_manager()
    {
        return $this->hasOne('App\Models\EmporeOrganisasiManager', 'id', 'empore_organisasi_manager_id');
    }

    /**
     * [empore_staff description]
     * @return [type] [description]
     */
    public function empore_direktur()
    {
        return $this->hasOne('App\Models\EmporeOrganisasiDirektur', 'id', 'empore_organisasi_direktur');
    }
}