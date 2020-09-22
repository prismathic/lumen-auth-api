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

    /**
     * Register as a new user
     *
     * @bodyParam name string required The name of the user
     * @bodyParam email string required The email of the user
     * @bodyParam password string required The password
     * @response {
     *  "success": true,
     *  "status": 201,
     *  "message": "User created successfully",
     *  "data": {
     *     "name": "Jesutomiwa Salam",
     *     "email": "tolak@gmail.com",
     *     "updated_at": "2020-09-22T06:53:20.000000Z",
     *     "created_at": "2020-09-22T06:53:20.000000Z",
     *     "id": 4
     *   }
     * }
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
     * User login endpoint, returns an instance of the user and an authentication access token
     *
     * @bodyParam email string required The email of the user
     * @bodyParam password string required The password
     * @response {
     *  "success": true,
     *  "status": 201,
     *  "message": null,
     *  "data": {
     *     "user": {
     *           "id": 4,
     *           "name": "Jesutomiwa Salam",
     *           "email": "tolak@gmail.com",
     *           "created_at": "2020-09-22T06:53:20.000000Z",
     *           "updated_at": "2020-09-22T06:53:20.000000Z"
     *      },
     *      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC9sb2NhbGhvc3Q6ODAwMFwvYXBpXC92MVwvbG9naW4iLCJpYXQiOjE2MDA3NTc3ODMsImV4cCI6MTYwMDc2MTM4MywibmJmIjoxNjAwNzU3NzgzLCJqdGkiOiI1b0FjTDg1MkYxN1hZTVJWIiwic3ViIjo0LCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.KoKXz3W-D0EhSA71ypI34RjfqCDRhLPlQzCgj5sOKm0",
     *      "token_type": "bearer",
     *       "expires_in": 3600
     *   }
     * }
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
