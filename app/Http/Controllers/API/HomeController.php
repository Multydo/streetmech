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

class HomeController extends Controller
{
    

    public function getCars(Request $requst){
        $sessionCon = new Session_controler() ;
        $data=[
            "key"=>"user_data",
            "data"=>""
        ];
        $session_info = $sessionCon->session_selector("get",$data); 
        
        if($session_info){
            $user_data = $session_info;
            $user_phone = $user_data["phone"];
            
            $cars = Car::where("phone",$user_phone)->select("id","plate","brand","year_model","color")->get();
            if(!$cars->isEmpty()){
                return response()->json([
                 "message"=>"user cars are found",
                 "state"=>true,
                 "cars"=>$cars
                ],200);
            }else{
                return response()->json([
                    "message" =>"no cars were found",
                    "state" =>false,
                    "cars"=>""
                ],200);
            }
           
           
            
        }else{
            return response()->json([
                "message" => "internal server error (problem in session)"
                
            ],500);
        }
    }

    public function addCars(Request $request){
        $sessionCon = new Session_controler() ;
        $data=[
            "key"=>"user_data",
            "data"=>""
        ];
        $session_info = $sessionCon->session_selector("get",$data); 
        if($session_info){
            $user_data = $session_info;
            $user_phone = $user_data["phone"];
            $carModel = new Car();
            $carModel->plate=$request->plate;
            $carModel->phone=$user_phone;
            $carModel->brand=$request->brand;
            $carModel->year_model=$request->year_model;
            $carModel->color=$request->color;
            $carModel->save();
            return response()->json([
                "message"=>"user new car is added",
                "state"=>true
            ],201);
        }else{
             return response()->json([
                "message" => "internal server error (problem in session)"
                
            ],500);
        }
    }


    public function addShops(Request $request){
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
                    $cityM = new BarrEliasWorker();
                    break;
                case "beirut" :
                    $cityM = new BeirutWorker();
                    break;
                case "borj_hammoud" :
                    $cityM = new BorjHammoudWorker();
                    break;
                case "sidon":
                    $cityM = new SidonWorker();
                    break;
                case "tripoli":
                    $cityM = new TripoliWorker();
                    break;
            }
            $cityM->shop_name = $request->shop_name;
                $cityM->street = $request->street;
                $cityM->more_details = $request->more_details;
                $cityM->profession = $request -> profession;
                $cityM->phone = $user_phone;
                $cityM->save();
                return response()->json([
                "message"=>"user new shop is saved"

            ],201);

        }else{
            return response()->json([
                "message" => "internal server error (problem in session)"
                
            ],500);
        }
    }
    public function selectCar(Request $request){
        $sessionCon = new Session_controler() ;
        $plate = $request->plate;
        $data=[
            "key"=>"user_car",
            "data"=> $plate
        ];
        $session_info = $sessionCon->session_selector("put",$data); 
        if($session_info){
            return true;
        }else{
            return false;
        }
    }


}