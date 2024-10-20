<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;


class UserController extends Controller {

    public function getUser($id) {
        $cachedUser = Redis::get("user:$id");

        if ($cachedUser) {
            return response()->json(json_decode($cachedUser), 200);
        }

        $user = User::find($id);
        Redis::set("user:$id", json_encode($user), 'EX', 86400);  // TTL 24 hours

        return response()->json($user, 200);
    }

    public function updateUser(Request $request, $id) {
        $user = User::find($id);
        $user->update($request->all());
        Redis::set("user:$id", json_encode($user), 'EX', 86400);  // Refresh cache

        return response()->json($user, 200);
    }
}
