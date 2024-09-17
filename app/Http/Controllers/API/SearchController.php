<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Session_controler;
use App\Models\User;
use App\Models\Car;
use App\Models\BarrEliasWorker;
use App\Models\BeirutWorker;
use App\Models\BorjHammoudWorker;
use App\Models\SidonWorker;
use App\Models\TripoliWorker;
use App\Models\order;

class SearchController extends Controller
{
    public function search(Request $request){
          $sessionCon = new Session_controler() ;
        $data=[
            "key"=>"user_data",
            "data"=>""
        ];
        $session_info = $sessionCon->session_selector("get",$data); 
        
        if($session_info){
            $user_data = $session_info;
            $user_phone = $user_data["phone"];
            $city = $request->city;
            $cityM='';
            switch ($city) {
                case "barr_elias":
                    $cityM = BarrEliasWorker::all();
                    break;
                case "beirut" :
                    $cityM =  BeirutWorker::all();
                    break;
                case "borj_hammoud" :
                    $cityM =  BorjHammoudWorker::all();
                    break;
                case "sidon":
                    $cityM =  SidonWorker::all();
                    break;
                case "tripoli":
                    $cityM =  TripoliWorker::all();
                    break;
            }
            if(!$cityM->isEmpty()){
                return response()->json([
                    "message"=>" we have shops",
                    "state"=>true,
                    "shops"=>$cityM
                
                ],200);
            }else{
                return response()->json([
                    "message"=>" we have shops",
                    "state"=>false,
                    "shops"=>""
                
                ],200);
            }
        
            $data=[
                "key"=>"user_city",
                "data"=>$city
            ];
            $sessionCon->session_selector("put",$data);
            
        }else{
             return response()->json([
                "message" => "internal server error (problem in session)"
                
            ],500);
        }

    }

    public function orders(Request $request){
        $sessionCon = new Session_controler() ;
        $data=[
            "key"=>"user_data",
            "data"=>""
        ];
        $session_info = $sessionCon->session_selector("get",$data); 
        if($session_info){
            $user_data = $session_info;
            $user_phone = $user_data["phone"];
            $city_data =[
                "key"=>"user_city",
                "data"=>""
            ];
            $session_city = $sessionCon->session_selector("get",$city_data); 
            $city = $session_city;

             $car_data =[
                "key"=>"user_car",
                "data"=>""
            ];
            $session_car = $sessionCon->session_selector("get",$car_data); 
            $plate = $session_car;
            $user_id = $request->id;
            $workerP = User::where("id",$user_id)->select("phone")->first();
            $orderM = new order();
            $orderM->client_p_nb = $user_phone ;
            $orderM->plate_nb = $plate;
            $orderM->worker_p_nb = $workerP;
            $orderM->city = $city;
            $orderM->save();
            $car_info = Car::where("plate",$plate)->get();
            return response()->json([
                "message"=>"request is set",
                "plate"=>$plate,
                "brand"=>$car_info["brand"],
                "year_model"=>$car_info["year_model"],
                "color"=>$car_info["color"],
                "workerP"=>$workerP,
                "city"=>$city
            ],201);

        }else{
            return response()->json([
                "message" => "internal server error (problem in session)"
                
            ],500);
        }
    }
}