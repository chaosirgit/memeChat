<?php
namespace App\Traits;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;


trait ResponseTrait{

    public function success($data = array(),$code = 200,$message = '请求成功'){
        $response = response()->json(['code'=>$code,'message'=>$message,'data'=>$data]);
        return $response;
    }

    public function error($message = '发生错误',$data = array(),$code = 400){
        $response = response()->json(['code'=>$code,'message'=>$message,'data'=>$data]);
        return $response;
    }

    public function responsePage(LengthAwarePaginator $paginateObject,$appends = array()){
        if (!empty($appends)){
            return $this->success(['pages'=>$paginateObject->lastPage(),'page'=>$paginateObject->currentPage(),'data'=>$paginateObject->items(),'total'=>$paginateObject->total(),'appends'=>$appends]);
        }else{
            return $this->success(['pages'=>$paginateObject->lastPage(),'page'=>$paginateObject->currentPage(),'data'=>$paginateObject->items(),'total'=>$paginateObject->total()]);
        }

    }

}
