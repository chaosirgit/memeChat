<?php
namespace App\Traits;


trait ToolsTrait
{

    public static function getTypeAndId($gatewayBindUid){
        $arr = explode('-',$gatewayBindUid);
        if (count($arr) != 2){
            throw new \Exception('解析 gatewayBindUid 错误');
        }
        if (!in_array($arr[0],['driver','company'])){
            throw new \Exception('解析 GatewayBindUid 错误');
        }
        return $arr;
    }



    public function diffTimeToFormat($seconds){
        if ($seconds > 3600){
            $hours = intval($seconds/3600);
            $minutes = $seconds % 3600;
            $time = $hours.":".gmstrftime('%M分%S秒', $minutes);
        }else{
            $time = gmstrftime('%H时%M分%S秒', $seconds);
        }
        return $time;
    }


    public function isExplodeArray($str,$delimit = ','){
        if (empty($str)){
            return false;
        }
        $str = rtrim($str,$delimit);
        $str = ltrim($str,$delimit);
        $arr = array_filter(explode($delimit,$str));
        if (!empty($arr)){
            return $arr;
        }
        return false;

    }

}