<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\API\Session_controler;
use Auth;
use App\Models\personal_access_token;
use Illuminate\Support\Carbon;


class UserControler extends Controller{

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     description="Registers a new user in the system with a unique phone number.",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","phone","password","role"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", example="strongpassword123"),
     *             @OA\Property(property="role", type="string", example="admin")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="accepted"),
     *             @OA\Property(property="state", type="boolean", example=true)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="User phone already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user phone already exists"),
     *             @OA\Property(property="state", type="boolean", example=false)
     *         ),
     *     ),
     * )
     */

    public function register(Request $request){
        $user_data = $request->all();
        
        $user_phone = $user_data['phone'];
        

        $user_phone_exists = User::where('phone',$user_phone)->exists();
        
        if($user_phone_exists){
          
                return response()-> json([
                    "message" => "user phone alredy exists",
                    "state"=>false
                ],409);
            
           
        }
        


        $user = new User();
        $user->name = $request->name;
       
       $user->role = $request -> role;
        $user->phone = $request->phone;
        $user->password = $request->password;
        
       

         $user->save();

         

         $token = $user->createToken($request['phone'])->plainTextToken;

        
        return response()->json([
                "message"=>"accepted",
                "state"=>true
        ],201);


        
    }
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     description="Log in a user with phone and password",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone","password"},
     *             @OA\Property(property="phone", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="user logged in"),
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="state", type="boolean", example=true)
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Phone or password do not match",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="phone or password do not match"),
     *             @OA\Property(property="state", type="boolean", example=false)
     *         ),
     *     )
     * )
     */


    public function login(Request $request){
        $credentials = $request->only('phone', 'password');
        if(Auth::attempt($credentials)){
            
            $user_phone = $request['phone'];
            $user_info = User::where('phone' , $user_phone)->select("role")->first();
            

            $old_token = personal_access_token::where("name",$user_phone);
            if($old_token){
                $old_token -> delete();
            }
            $user = Auth::user();
            $token = $user->createToken($user_phone)->plainTextToken;
            $tokenStatus = personal_access_token::find( $token);
            $tokenStatus->last_used_at = Carbon::now();
            $tokenStatus->save();
            $sessionCon = new Session_controler ;
            $user_data=[
                "phone"=>$user_phone,
                "role" =>$user_info["role"],
                "plate"=>"",
                "wphone"=>"",
                "city"=>""
            ];
            $data=[
                "key"=>"user_data",
                "data"=>$user_data
            ];
            $sessionCon->session_selector("put",$data);
            return response() -> json([
                "massage"=>"user loged in",
                "role"=>$user_info["role"],
                "state" => true
            ],200);
            
            

        }else{
            return response()->json([
                "message"=>"phone or password do not match",
                "role"=>false,
                "state"=>false
            ],401);
        }
    }
}