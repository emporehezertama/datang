<?php

namespace App\Http\Controllers;

use App\Models\CrmModule;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\CrmProduct;
use App\Models\CrmProjects;

class HrisController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //
    public function getModule(Request $request) 
    {
        // return product
        // return project_type, expired_date, lisence_number
        $product = CrmProduct::where('parent_id',1)->get();
        $project = CrmProjects::where('id',$request->get('project_id'))->get();
        
        return response()->json(['status' => "success", "product" => $product, "project"=>$project], 200);
    }
    
}
