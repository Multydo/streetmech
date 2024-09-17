<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\API\Session_controler;
use Auth;
use App\Models\personal_access_token;
use Illuminate\Support\Carbon;


class UserControler extends Controller
{
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