<?php

namespace App\Http\Controllers;

use App\Models\CrmModule;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\CrmProduct;
use App\Models\CrmProjects;
use App\Models\CrmProjectProduct;


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

    public function updateModuleCrm(Request $request)
    {
        //ke database crm ke tabel project_product
        if($request->get('project_id') != null) {

            CrmProjectProduct::where('crm_product_id',$request->get('crm_product_id'))->where('crm_project_id',$request->get('project_id'))->delete();

                $product = CrmProjectProduct::where('crm_product_id',$request->get('crm_product_id'))->where('crm_project_id',$request->get('project_id'))->first();

                if(!$product)
                {
                    $product = new CrmProjectProduct();
                    $product->crm_project_id  = $request->get('project_id');
                    $product->crm_product_id  = $request->get('crm_product_id');
                    if($request->get('limit_user') != null){
                        $product->limit_user      = $request->get('limit_user');
                    }
                    $product->save();
                }

        } else{
            CrmProjectProduct::where('crm_project_id',$request->get('project_id'))->delete();
        }
        return response()->json(['status' => "success"], 201);
    }
    
}
