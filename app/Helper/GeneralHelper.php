<?php
/**
 * Get Setting
 * @param  $key
 * @return string
 */
function get_setting($key)
{
	$setting = \App\Models\Setting::where('key', $key)->first();
	if($key == 'layout_karyawan'){
		if($setting)
		{
			return $setting->value;
		}
	}
	$auth = \Auth::user();
	if($auth)
	{
		if($auth->project_id != NULL)
        {
        	$setting = \App\Models\Setting::where('key', $key)->where('project_id',$auth->project_id)->first();
        }else{
        	$setting = \App\Models\Setting::where('key', $key)->first();
        }
	}else{
		$setting = \App\Models\Setting::where('key', $key)->first();
	}

	if($setting)
	{
		return $setting->value;
	}
	
	return '';
}