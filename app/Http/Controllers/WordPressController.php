<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WordPressController extends Controller
{
    // 方法：新增使用者
    public function createUser(Request $request)
    {
        $response = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                        ->post(env('WORDPRESS_URL') . '/wp-json/wp/v2/users', [
                            'username' => $request->username,
                            'password' => $request->password,
                            'email' => $request->email,
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'roles' => ['subscriber'] // 預設 訂閱者
                        ]);

        return $response->body();
    }

    // 方法：修改使用者密碼
    public function updateUserPassword(Request $request, $userId)
    {
        $username = $request->username; // 用户提交的用户名
        $newPassword = $request->new_password; // 用户提交的新密码

        // 首先通过用户名查找用户ID
        $userResponse = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                            ->get(env('WORDPRESS_URL') . '/wp-json/wp/v2/users', [
                                'search' => $username
                            ]);

        $users = $userResponse->json();

        if (empty($users)) {
            return response()->json(['error' => "No user found with username: {$username}"], 404);
        }

        // 假设 search 返回至少一个用户，并取第一个用户的 ID
        $userId = $users[0]['id'];

        // 使用获得的用户 ID 更新密码
        $response = Http::withBasicAuth(env('WORDPRESS_USER'), env('WORDPRESS_PASSWORD'))
                        ->post(env('WORDPRESS_URL') . "/wp-json/wp/v2/users/{$userId}", [
                            'password' => $newPassword
                        ]);

        // 返回 API 的响应
        return $response->body();
    }
}
