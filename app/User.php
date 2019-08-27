<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

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
}
