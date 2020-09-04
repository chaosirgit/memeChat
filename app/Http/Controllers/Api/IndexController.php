<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GatewayWorker\Lib\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function upload(Request $request)
    {
        $avatar = $request->get('avatar', null);
        if (empty($avatar)) {
            return $this->error('请上传图片');
        }
        try {
            DB::beginTransaction();
            $user         = auth()->user();
            $user->avatar = $avatar;
            $user->save();
            DB::commit();
            return $this->success($user);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function isConnect()
    {
        $user                     = auth()->user();
        Gateway::$registerAddress = config('gateway.code_register');
        $client_id = Gateway::getClientIdByUid($user->id);
        $count = Gateway::getAllClientCount();
        if (empty($client_id)) {
            return $this->success(['is_online' => false,'online_count'=>$count]);
        } else {
            return $this->success(['is_online' => true,'online_count'=>$count]);
        }

    }

    public function bindSocket(Request $request)
    {
        $client_id = $request->get('client_id', '');
        $user      = auth()->user();
        if (empty($client_id)) {
            return $this->error('参数不完整');
        }
        try {
            Gateway::$registerAddress = config('gateway.code_register');
            $clients                  = Gateway::getClientIdByUid($user->id);
            if (!empty($clients)) {
                foreach ($clients as $client) {
                    Gateway::closeClient($client);
                }
            }
            Gateway::bindUid($client_id,$user->id);
            $count = Gateway::getAllClientCount();
            return $this->success(['online_count'=>$count]);
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
