<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreArea;
use App\Models\ReportProduct;
use DB;

class ApiHomeController extends Controller
{
    public function getArea(Request $request){
        $area = StoreArea::get();
        return response()->json([
            'status' => true,
            'data' => $area,
        ]);
    }

    public function getComplianceSeries(Request $request){
        $req = json_decode($request->getContent(), true);

        $data = ReportProduct::select(DB::raw("sum(compliance) as comply"), DB::raw("count(compliance) as total"), "store_area.area_name", "store_area.area_id")
                                ->join('store', 'store.store_id', '=', 'report_product.store_id')
                                ->join('store_area', 'store_area.area_id', '=', 'store.area_id')
                                ->groupBy('store_area.area_id');

        if ($req) {
            if ($req['areas'] != null) {
                $data = $data->where(function($query) use ($req){
                    foreach ($req['areas'] as $value) {
                        // dd($value);
                        $query->orWhere('store_area.area_id', '=', $value);
                    }
                });
            };
            if ($req['start_date']) {
                $data = $data->where("tanggal",">=", $req['start_date']);
            };

            if ($req['end_date']) {
                
                $data = $data->where("tanggal","<=", $req['end_date']);
            };
        }
        $data = $data->get();

        $data_compliance = [];
        foreach ($data as $key => $value) {
            $tmp = [
                "name" => $value->area_name,
                "y" => ($value->comply / $value->total ) * 100
            ];
            array_push($data_compliance, $tmp);
        }
        return response()->json([
            'status' => true,
            'data' => $data_compliance,
        ]);

    }

    public function getComplianceTable(Request $request){
        $req = json_decode($request->getContent(), true);

        $area = new StoreArea();

        $data = ReportProduct::select(DB::raw("sum(compliance) as comply"), DB::raw("count(compliance) as total"), "store_area.area_name", "store_area.area_id", "store_area.area_id", "product.product_name")
                                ->join('store', 'store.store_id', '=', 'report_product.store_id')
                                ->join('store_area', 'store_area.area_id', '=', 'store.area_id')
                                ->join('product', 'product.product_id', '=', 'report_product.product_id')
                                ->groupBy('store_area.area_id', 'product.product_name');

        if ($req) {
            if ($req['areas'] != null) {
                $data = $data->where(function($query) use ($req){
                    foreach ($req['areas'] as $value) {
                        // dd($value);
                        $query->orWhere('store_area.area_id', '=', $value);
                    }
                });
                $area = $area->where(function($query) use ($req){
                    foreach ($req['areas'] as $value) {
                        // dd($value);
                        $query->orWhere('area_id', '=', $value);
                    }
                });
            };
            if ($req['start_date']) {
                $data = $data->where("tanggal",">=", $req['start_date']);
            };

            if ($req['end_date']) {
                
                $data = $data->where("tanggal","<=", $req['end_date']);
            };
        }
        // dd($data->toSql());
        $data = $data->get();
        $area = $area->get();

        $data_compliance = [];

        
        foreach ($data as $key => $value) {
            if (!isset($data_compliance[$value->product_name])) {
                $data_compliance[$value->product_name] = [];
            }
            $data_compliance[$value->product_name][$value->area_id] = ($value->comply / $value->total ) * 100;
            // array_push($data_compliance[$value->product_name], [
            //     $value->area_id => 
            // ]);
        }

       
        return response()->json([
            'status' => true,
            'data' => $data_compliance,
            'area' => $area,
        ]);

    }
}
