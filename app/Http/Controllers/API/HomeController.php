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
    /**
     * @OA\Post(
     *     path="/getcars",
     *     summary="Get user cars",
     *     description="Fetches all the cars associated with the user session",
     *     tags={"Home"},
     *     @OA\Response(
     *         response=200,
     *         description="List of user cars",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user cars are found"),
     *             @OA\Property(property="state", type="boolean", example=true),
     *             @OA\Property(property="cars", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="plate", type="string", example="ABC123"),
     *                 @OA\Property(property="brand", type="string", example="Toyota"),
     *                 @OA\Property(property="year_model", type="string", example="2015"),
     *                 @OA\Property(property="color", type="string", example="Red")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         )
     *     )
     * )
     */
    

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
    /**
     * @OA\Post(
     *     path="/addcars",
     *     summary="Add a new car",
     *     description="Adds a new car to the user's account",
     *     tags={"Home"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plate", "brand", "year_model", "color"},
     *             @OA\Property(property="plate", type="string", example="ABC123"),
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="year_model", type="string", example="2015"),
     *             @OA\Property(property="color", type="string", example="Red")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="New car added",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user new car is added"),
     *             @OA\Property(property="state", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         )
     *     )
     * )
     */

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
    /**
     * @OA\Post(
     *     path="/addShops",
     *     summary="Add a new shop",
     *     description="Adds a new shop based on the city to the user's account",
     *     tags={"Home"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city", "shop_name", "street", "more_details", "profession"},
     *             @OA\Property(property="city", type="string", example="beirut"),
     *             @OA\Property(property="shop_name", type="string", example="John's Garage"),
     *             @OA\Property(property="street", type="string", example="Main Street"),
     *             @OA\Property(property="more_details", type="string", example="Near the central park"),
     *             @OA\Property(property="profession", type="string", example="Mechanic")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="New shop added",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user new shop is saved")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         )
     *     )
     * )
     */


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
    /**
     * @OA\Post(
     *     path="/selectcar",
     *     summary="Select a car",
     *     description="Selects a car for the current session",
     *     tags={"Home"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"plate"},
     *             @OA\Property(property="plate", type="string", example="ABC123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car selected successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="car selected"),
     *             @OA\Property(property="state", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         )
     *     )
     * )
     */
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