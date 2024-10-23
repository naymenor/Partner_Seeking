<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\CustomerProfile;

class UserAuthController extends Controller
{
    //
    public function userlist()
    {
        $userlist = User::get();

        if ($userlist->isNotEmpty()) {

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $userlist,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ], 404);
        }
    }
    
    public function register(Request $request){
        $registerUserData = $request->validate([
            'mobile'=>'required|string|unique:users',
            'email'=>'required|string|email|unique:users',
            'password'=>'required|min:8',
        ]);
        $user = User::create([
            'mobile' => $registerUserData['mobile'],
            'email' => $registerUserData['email'],
            'password' => Hash::make($registerUserData['password']),
            'userRoleId' => 'customer'
        ]);
        if($user)
        {   
            $uuid = Str::uuid()->toString();
            $Crprofile = CustomerProfile::create([
                'uuid' => $uuid,
                'user_id' => $user->id,
                'personal_infos' => json_encode($request->personal_infos) ?? null,
                'demographic_infos' => json_encode($request->demographic_infos) ?? null,
                'educational_infos' => json_encode($request->educational_infos) ?? null,
                'employment_infos' => json_encode($request->employment_infos) ?? null,
                'marital_infos' => json_encode($request->marital_infos) ?? null,
                'referees_infos' => json_encode($request->referees_infos) ?? null,
                'religious_infos' => json_encode($request->religious_info) ?? null,
                'is_verified' => 0,
                'created_by' => 'SYSTEM',
                'status' => 0,
            ]);
        }
        return response()->json([
            'message' => 'User Created ',
            'data' => $Crprofile
        ]);
    }


    public function login(Request $request){

        $validate = Validator::make(
            $request->all(),
            [
                'mobile' => 'required',
                'password' => 'required',
            ]
        );

        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('mobile', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $authuser = Auth::user();
        $token = $authuser->createToken('API Token')->plainTextToken;
       
        return response()->json([
            'access_token' => $token,
            "user_id" => $authuser->id,
            'user_email' => $authuser->email,
            'user_role' => $authuser->userRoleId,
            'user_mobile' => $authuser->mobile,
        ]);
    }
//test data
    public function logout(){
        auth()->user()->tokens()->delete();
    
        return response()->json([
          "message"=>"logged out"
        ]);
    }
}
