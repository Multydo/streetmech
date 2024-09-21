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

    /**
     * @OA\Post(
     *     path="/search",
     *     summary="Search for shops",
     *     description="Searches for available shops in a specified city",
     *     tags={"Search"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city"},
     *             @OA\Property(property="city", type="string", example="beirut")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of shops found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="we have shops"),
     *             @OA\Property(property="state", type="boolean", example=true),
     *             @OA\Property(property="shops", type="array", @OA\Items(type="object"))
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         ),
     *     ),
     * )
     */
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
    /**
     * @OA\Post(
     *     path="/order",
     *     summary="Place an order",
     *     description="Creates an order for a car repair based on user and session data",
     *     tags={"Search"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"id"},
     *             @OA\Property(property="id", type="integer", example=1)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Order successfully created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="request is set"),
     *             @OA\Property(property="plate", type="string", example="ABC123"),
     *             @OA\Property(property="brand", type="string", example="Toyota"),
     *             @OA\Property(property="year_model", type="string", example="2015"),
     *             @OA\Property(property="color", type="string", example="Red"),
     *             @OA\Property(property="workerP", type="string", example="1234567890"),
     *             @OA\Property(property="city", type="string", example="beirut")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="internal server error (problem in session)")
     *         ),
     *     ),
     * )
     */

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