<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Room;
use App\RoomUser;
use GatewayWorker\Lib\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoomController extends Controller
{
    public function createRoom(Request $request) {
        $name = $request->get('name','');
        if (!is_numeric($name)){
            return $this->error('房间名称只能是数字');
        }
        if (!is_int($name)){
            return $this->error('房间名称只能是数字');
        }
        if (empty($name) || $name < 0){
            return $this->error('暂不开放 0 号房间或负数房间');
        }
        $user = auth()->user();
        try{
            DB::beginTransaction();
            $room = Room::query()->lockForUpdate()->where('name',$name)->first();
            if (empty($room)){
                $room = new Room();
                $room->name = $name;
                $room->user_id = $user->id;
                $room->save();
            }
            $exist = RoomUser::query()->lockForUpdate()->where('user_id',$user->id)->where('room_id',$room->id)->first();
            if (empty($exist)){
                $room_user = new RoomUser();
                $room_user->room_id = $room->id;
                $room_user->user_id = $user->id;
                $room_user->save();
            }
            DB::commit();
            Gateway::$registerAddress = config('gateway.code_register');
            $client_id = Gateway::getClientIdByUid($user->id);
            Gateway::joinGroup(current($client_id),$room->id);
            $data = array();
            $data['user'] = $user;
            $data['is_self'] = 2;
            $data['msg'] = $user->username.' 进入房间了';
            $data['type'] = 'message';
            $message = json_encode($data,true);
            Gateway::sendToGroup($room_user->room_id,$message);
            return $this->success();

        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function roomInfo() {
        $user = auth()->user();
        $user_room = RoomUser::query()->where('user_id',$user->id)->with(['room'])->first();
        Gateway::$registerAddress = config('gateway.code_register');
        $group_count = Gateway::getClientCountByGroup($user_room->room_id);
        return $this->success(['user_room'=>$user_room,'group_count'=>$group_count]);
    }

    public function outRoom(){
        $user = auth()->user();
        try{
            DB::beginTransaction();
            $room_user = RoomUser::query()->where('user_id',$user->id)->lockForUpdate()->first();
            $room_id = $room_user->room_id;
            if (!empty($room_user)){
                $room_user->delete();
                Gateway::$registerAddress = config('gateway.code_register');
                $client_id = Gateway::getClientIdByUid($user->id);
                Gateway::leaveGroup(current($client_id),$room_id);
            }
            DB::commit();
            $data = array();
            $data['user'] = $user;
            $data['is_self'] = 2;
            $data['msg'] = $user->username.' 离开房间了';
            $data['type'] = 'message';
            $message = json_encode($data,true);
            Gateway::sendToGroup($room_id,$message);
            return $this->success();
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->error($exception->getMessage());
        }
    }

    public function sendMsg(Request $request){
        $msg = $request->get('msg','');
        if (empty($msg)){
            return $this->error('不能发送空消息');
        }
        $user = auth()->user();
        try{
            Gateway::$registerAddress = config('gateway.code_register');

            $room_user = RoomUser::query()->where('user_id',$user->id)->first();
            if (empty($room_user)){
                return $this->error('您还未进入房间,请重新进入');
            }
            $group_uid = Gateway::getUidListByGroup($room_user->room_id);
            foreach ($group_uid as $uid){
                $data = array();
                $data['user'] = $user;
                if ($uid == $user->id){
                    $data['is_self'] = 1;
                }else{
                    $data['is_self'] = 0;
                }
                $data['msg'] = $msg;
                $data['type'] = 'message';
                $message = json_encode($data,true);
                Gateway::sendToUid($uid,$message);
            }
            return $this->success();
        }catch (\Exception $exception){
            return $this->error($exception->getMessage());
        }
    }
}
