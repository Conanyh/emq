<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use EasyWeChatComposer\EasyWeChat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function weappStore(Request $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = EasyWeChat::miniProgram();
        $data = $miniProgram->auth->session($code);

        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];

        if (!$user) {
            if (!$request->username) {
                return $this->response->errorForbidden('用户不存在');
            }

            $username = $request->userename;

            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;

            $credentials['password'] = $request->password;

            if (!Auth::guard('api')->once($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }

            // 获取对应的用户
            $user = Auth::guard('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];
        }

        // 更新用户数据
        $user->update($attributes);

        $token = Auth::guard('api')->formUser($user);

        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function login()
    {
        $credentials = \request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);

    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expire_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function test(Request $request)
    {

    }


}
