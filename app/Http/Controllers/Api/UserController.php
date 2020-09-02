<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Api\UserTrait;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use UserTrait;

    public function login(Request $request){
        $code = $request->get('code',''); //code
        if (empty($code)){
            return $this->error('请填写完整');
        }
        $session = $this->app()->auth->session($code);
        //array:2 [
        //  "session_key" => "QlyTIaYIHEM7MfN98OBQWA=="
        //  "openid" => "o5TiR4jonhjes2Jcs6PfG3_WTCMk"
        //]
        if (empty($session['openid']) || empty($session['session_key'])){
            return $this->error('微信服务器连接失败');
        }

        try{
            $has = User::where('openid',$session['openid'])->first();
            if (empty($has)){
                $user = new User();
                $user->openid = $session['openid'];
                $user->session_key = $session['session_key'];
                $user->save();
                $token = $user->createToken('Deliverer',['*'])->accessToken;
            }else{
                $has->session_key = $session['session_key'];
                $has->save();
                $token = $has->createToken('Deliverer',['*'])->accessToken;
            }

            return $this->success([
                'token_type' => 'Bearer',
                'access_token' => $token,
            ]);

        }catch (\Exception $exception){
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
}
