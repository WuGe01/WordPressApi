<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WordPressController extends Controller
{
    public function createUser(Request $request)
    {
        $response = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                        ->post(env('WORDPRESS_URL') . '/wp-json/wp/v2/users', [
                            'username' => $request->username,
                            'password' => $request->password,
                            'email' => $request->email,
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'roles' => ['subscriber']
                        ]);

        return $response->body();
    }

    public function updateUserPassword(Request $request)
    {
        $username = $request->username;
        $newPassword = $request->new_password;

        $userResponse = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                            ->get(env('WORDPRESS_URL') . '/wp-json/wp/v2/users', [
                                'search' => $username
                            ]);

        $users = $userResponse->json();

        if (empty($users)) {
            return response()->json(['error' => "No user found with username: {$username}"], 404);
        }

        $userId = $users[0]['id'];

        $response = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                        ->post(env('WORDPRESS_URL') . "/wp-json/wp/v2/users/{$userId}", [
                            'password' => $newPassword
                        ]);

        return $response->body();
    }
}
