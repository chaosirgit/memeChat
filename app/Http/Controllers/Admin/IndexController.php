<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class IndexController extends Controller
{
    /**
     * 登录
     *
     * @param AdminLogin $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(AdminLogin $request)
    {
        $username = $request->get('username'); // 用户名
        $password = $request->get('password'); // 密码
        try {
            $user = Admin::where('username', $username)->first();
            if (empty($user)) {
                return $this->error('账号或密码错误');
            }
            if (!Hash::check($password, $user->password)) {
               return $this->error('账号或密码错误');
            }
            $token = $user->createToken('Admin', ['*'])->accessToken;
            return $this->success([
                'token_type'   => 'Bearer',
                'access_token' => $token,
            ]);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }


    public function info(){
        $user = auth()->user();
        $user->roles = ['admin'];
        return $this->success($user);
    }

    /**
     * 登出
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->user()->token()->delete();
        return $this->success('登出成功');
    }



    /**
     * 上传图片
     * @param Request $request
     */
    public function upload(Request $request){
        $file = $request->file('file'); //上传图片
        if (empty($file)){
            return $this->error('请上传图片');
        }
        $mime_type = $file->getMimeType();
        if ($mime_type != 'image/jpeg' && $mime_type != 'image/png'){
            return $this->error('请上传格式为 jpg 或 png 的图片');
        }
        $size = $file->getSize();
        if ($size > 4194304){
            return $this->error('请上传不超过4M的图片');
        }
        $path = asset('storage/'.$file->storePublicly('image','public'));
        return $this->success($path);
    }
}
