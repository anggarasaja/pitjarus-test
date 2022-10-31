<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreArea;

class HomeController extends Controller
{
    public function index(Request $request){
        $data["tableHead"] = StoreArea::get();
        return view('welcome', $data);
    }
}
