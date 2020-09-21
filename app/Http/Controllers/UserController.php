<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        return $this->middleware('auth');
    }

    /*


    */
    public function getAll(Request $request) {

        $perPage = $request->perPage ? $request->perPage : 20;

        if ($request->q) {
            $users = User::where('name', 'like', '%' . $request->q . '%')->orWhere('email', 'like', '%' . $request->q . '%')->orderBy('name')->simplePaginate($perPage);
        }

        else {
            $users = User::orderBy('name')->simplePaginate($perPage);
        }

        return $this->sendFormattedJsonResponse($users);
    }

    /*
        * @group hjh
        * Get details for a single user
        * id required The id of the specified user
        *
    */
    public function getSingle($id) {
        $user = User::findOrFail($id);

        return $this->sendFormattedJsonResponse($user);
    }

    /*
    *
    *
    *
    */
    public function getUserProfile() {
        $user = User::findOrFail(Auth::id());

        return $this->sendFormattedJsonResponse($user);
    }


}
