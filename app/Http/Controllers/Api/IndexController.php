<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function upload(Request $request){
        $avatar = $request->get('avatar',null);
        if (empty($avatar)){
            return $this->error('请上传图片');
        }
        try{
            DB::beginTransaction();
            $user = auth()->user();
            $user->avatar = $avatar;
            $user->save();
            DB::commit();
            return $this->success($user);
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }
}
