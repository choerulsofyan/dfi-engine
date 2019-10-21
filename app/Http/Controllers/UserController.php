<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'created_at as register_date')->orderBy('name', 'ASC')->get();
        $data = array("status" => 200, "results" => $users);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
			'name' => 'required', 
			'email' => 'required|email', 
			'password' => 'string', 
			'confirm_password' => 'string|same:password', 
		]); 

		if ($validator->fails()) { 
			return response()->json(['error'=>$validator->errors()], 401); 
		} 

		$input = $request->all(); 

        if (isset($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        }

		$user->update($input);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json('User deleted successfully');
    }

    public function logout (Request $request) {

        $token = $request->user()->token();
        $token->revoke();
    
        $response = 'You have been succesfully logged out!';
        return response($response, 200);
    
    }
}
