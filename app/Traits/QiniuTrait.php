<?php
namespace App\Traits;

use Qiniu\Auth;

trait QiniuTrait{

    public function getUpToken(){
        $accessKey = config('filesystems.disks.qiniu.accessKey');
        $secretKey = config('filesystems.disks.qiniu.secretKey');
        $bucket = config('filesystems.disks.qiniu.bucket');

        //构建鉴权对象
        $auth = new Auth($accessKey,$secretKey);

        //生成上传 token
        $token = $auth->uploadToken($bucket);

        return $token;
    }

    public function getQiniuCndDomain(){
        return config('filesystems.disks.qiniu.cdn_domain');
    }

}