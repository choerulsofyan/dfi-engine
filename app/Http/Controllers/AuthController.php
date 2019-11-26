<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request) 
	{ 
		$validator = Validator::make($request->all(), [
			'name' => 'required', 
			'email' => 'required|email', 
			'password' => 'required', 
			'confirm_password' => 'required|same:password', 
		]); 

		if ($validator->fails()) { 
			return response()->json(['error'=>$validator->errors()], 401); 
		} 

		$input = $request->all(); 

		$input['password'] = bcrypt($input['password']);

		$user = User::create($input);

		$success['token'] = $user->createToken('myApp')->accessToken;

		$success['name'] = $user->name; return response()->json(['success'=>$success], 200);
	}

	public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($credentials)) {
			// $token = auth()->user()->createToken('myApp')->accessToken;
			$user = auth()->user();
			$token = $user->createToken('myApp')->accessToken;

			unset($user['email_verified_at']);
			unset($user['created_at']);
			unset($user['updated_at']);

			$user['token'] = $token;

            return response()->json(['user' => $user], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }
	
	public function details()
    {
        return response()->json(['user' => auth()->user()], 200);
    }
}
