<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Token;
use App\User;
use Illuminate\Support\Facades\Auth;

class UsersControllerAPI extends Controller
{
    public function login(LoginRequest $request){

        $user = User::where('email', $request->email)->first();

        if($user->blocked){
            return response()->json('User blocked', 500);
        }

        $http = new \GuzzleHttp\Client;
        $response = $http->post(env('APP_URL').'/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => env('PASSWORD_CLIENT_ID'),
                'client_secret' => env('PASSWORD_CLIENT_SECRET'),
                'username' => $request->email,
                'password' => $request->password,
                'scope' => ''
            ],
            'exceptions' => true,
        ]);
        $errorCode= $response->getStatusCode();
        if ($errorCode=='200') {
            return json_decode((string) $response->getBody(), true);
        } else {
            return response()->json(
                ['msg'=>'User credentials are invalid',"body"=>$response->getBody()], $errorCode);
        }
    }

    public function logout(){
        auth()->guard('api')->user()->token()->revoke();
        auth()->guard('api')->user()->token()->delete();
        return response()->json(['msg'=>'Token revoked'], 200);
    }

    public function register(RegisterRequest $request){

    }
}
