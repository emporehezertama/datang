<?php

namespace App\Http\Controllers;

use App\Models\CrmModule;
use App\Models\Users;
use Illuminate\Http\Request;
use App\Models\CrmProduct;
use App\Models\CrmProjects;
use App\Models\CrmProjectProduct;
use App\Models\CrmPricelistHistoryDetail;
use DB;


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
    //    $product = CrmProduct::where('parent_id',1)->get();
        $lastid = CrmPricelistHistoryDetail::latest('history_id')->first();
        $product = CrmProduct::join('crm_pricelist_history_detail', 'crm_product.id', '=', 'crm_pricelist_history_detail.price_id')
                                ->select('crm_product.name', 'crm_pricelist_history_detail.price', 'crm_product.id', 'crm_product.user_limit')
                                ->where('crm_product.parent_id',1)
                                ->where('crm_pricelist_history_detail.history_id', $lastid->history_id)
                                ->get();
        $project = CrmProjects::where('id',$request->get('project_id'))->get();
        
        return response()->json(['status' => "success", "product" => $product, "project"=>$project], 200);
    }

    public function updateModuleCrm(Request $request)
    {
        //ke database crm ke tabel project_product
        if($request->get('crm_product_id') != null) {

            $arrayProduct = explode(',',$request->get('crm_product_id'));
            $dataProduct = array_map('intval',$arrayProduct);

            CrmProjectProduct::whereNotIn('crm_product_id',$dataProduct)->where('crm_project_id',$request->get('crm_project_id'))->delete();

                
                foreach ($dataProduct as $key => $value) {
                    # code...
                    $product = CrmProjectProduct::where('crm_product_id',$value)->where('crm_project_id',$request->get('crm_project_id'))->first();
                    if(!$product)
                    {
                        $product = new CrmProjectProduct();
                        $product->crm_project_id  = $request->get('crm_project_id');
                        $product->crm_product_id  = $value;
                    }
                    if($value == 3){
                        $product->limit_user      = $request->get('limit_user');
                    }
                        $product->save();
                }
        } else{
            CrmProjectProduct::where('crm_project_id',$request->get('crm_project_id'))->delete();
        }
        return response()->json(['status' => "success"], 201);

    }
    
}
