<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /*


    */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), array(
            'name' => 'required|max:255',
            'email'  =>  'required|unique:users|email:rfc',
            'password' => 'required|unique:users'
        ));

        if ($validator->fails()) {
            return $this->sendJsonErrorResponse($validator->errors()->first(),400);
        }

        try {

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            $user->save();

            return $this->sendFormattedJsonResponse($user, "User created successfully", 201);

        } catch (\Throwable $th) {
            return $this->sendJsonErrorResponse("Sorry, there was an error during registration, please try again");
        }
    }

    /**
        * @group hjh
        * Get details for a single user
        * id required The id of the specified user
        *
    */
    public function login(Request $request) {
        $body= $request->only('email','password');

        if (! $token = Auth::attempt($body)) {
            return $this->sendJsonErrorResponse("Invalid username or password", 401);
        }

        $responseData = [
            'user' => Auth::user(),
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ];

        return $this->sendFormattedJsonResponse($responseData);
    }


}
