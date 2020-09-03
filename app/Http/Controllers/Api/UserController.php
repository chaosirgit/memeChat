<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function login(Request $request){
        $username = $request->get('username','');
        $password = $request->get('password','');
        $gender = $request->get('gender',1);
        if (empty($username) || empty($password)){
            return $this->error('请填写完整');
        }

        try{
            DB::beginTransaction();
            $has = User::query()->lockForUpdate()->where('username',$username)->first();
            if (empty($has)){
                $user = new User();
                $user->username = $username;
                $user->password = Hash::make($password);
                $user->gender = $gender;
                $user->save();
                $token = $user->createToken('User',['*'])->accessToken;
            }else{
                if (!Hash::check($password,$has->password)){
                    DB::rollBack();
                    return $this->error('密码错误');
                }
                $token = $has->createToken('User',['*'])->accessToken;
            }
            DB::commit();
            return $this->success([
                'token_type' => 'Bearer',
                'access_token' => $token,
            ]);

        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function user(){
        $user = auth()->user();
        return $this->success($user);
    }

    public function bindUserInfo(Request $request){
        $avatar = $request->get('avatar',''); //头像
        $nickname = $request->get('nickname',''); //昵称
        $user = auth()->user();
        if (empty($avatar) || empty($nickname)){
            return $this->error('请求参数不完整');
        }
        try{
            DB::beginTransaction();
            $user->avatar = $avatar;
            $user->nickname = $nickname;
            $user->save();
            DB::commit();
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }


    public function bindUserPhone(Request $request){
        $iv = $request->get('iv',''); //iv
        $encryptedData = $request->get('encryptedData',''); //加密数据

        $user = auth()->user();
        if (empty($iv) || empty($encryptedData)){
            return $this->error('请求参数不完整');
        }
        $decryptedData = $this->app()->encryptor->decryptData($user->session_key, $iv, $encryptedData);
        if (empty($decryptedData['purePhoneNumber'])){
            return $this->error('获取微信绑定手机号失败，请在微信绑定手机号');
        }
        try{
            DB::beginTransaction();

            $user->phone = $decryptedData['purePhoneNumber'];

            $user->save();

            DB::commit();
            return $this->success($user->phone);
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function logout() {
        $user = auth()->user();
        try{
            $user->token()->delete();
            return $this->success();
        }catch (\Exception $exception){
            return $this->error('Exception Error');
        }
    }

}
