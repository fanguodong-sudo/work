<?php

namespace App\Http\Controllers;


use \Illuminate\Http\Request as Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class requestController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function __construct(){

    }

    public function update(Request $request,$id){
        echo "id:".$id;
        print_r($request->headers);
        exit(0);
    }
}
