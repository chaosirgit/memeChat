<?php

namespace App\Workerman;


use GatewayWorker\Lib\Gateway;

class Events
{
    /**
     * 当businessWorker进程启动时触发。每个进程生命周期内都只会触发一次。
     * @param $businessWorker businessWorker进程实例
     */
    public static function onWorkerStart($businessWorker)
    {
    }

    /**
     * 当客户端连接上gateway进程时(TCP三次握手完毕时)触发的回调函数。
     * @param $client_id 固定为20个字符的字符串，用来全局标记一个socket连接，每个客户端连接都会被分配一个全局唯一的client_id。如果client_id对应的客
     *                   户端连接断开了，那么这个client_id也就失效了。当这个客户端再次连接到Gateway时，将会获得一个新的client_id。也就是说
     *                   client_id和客户端的socket连接生命周期是一致的。
     *                   client_id一旦被使用过，将不会被再次使用，也就是说client_id是不会重复的，即使分布式部署也不会重复。
     *                   只要有client_id，并且对应的客户端在线，就可以调用Gateway::sendToClient($client_id, $data)等方法向这个客户端发送数据。
     */
    public static function onConnect($client_id)
    {
//        Gateway::sendToCurrentClient("Your client_id is $client_id");
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'init',
            'client_id' => $client_id
        )));
    }

    /**
     * 当客户端连接上gateway完成websocket握手时触发的回调函数。
     * 注意：此回调只有gateway为websocket协议并且gateway没有设置onWebSocketConnect时才有效。
     * @param $client_id 固定为20个字符的字符串，用来全局标记一个socket连接，每个客户端连接都会被分配一个全局唯一的client_id。
     * @param $data websocket握手时的http头数据，包含get、server等变量
     */
    public static function onWebSocketConnect($client_id, $data)
    {
    }

    /**
     * 当客户端发来数据(Gateway进程收到数据)后触发的回调函数
     * @param $client_id 全局唯一的客户端socket连接标识
     * @param $message 完整的客户端请求数据，数据类型取决于Gateway所使用协议的decode方法返的回值类型
     */
    public static function onMessage($client_id, $message)
    {

    }

    /**
     * 客户端与Gateway进程的连接断开时触发。不管是客户端主动断开还是服务端主动断开，都会触发这个回调。一般在这里做一些数据清理工作。
     * 注意：onClose回调里无法使用Gateway::getSession()来获得当前用户的session数据，但是仍然可以使用$_SESSION变量获得。
     * 注意：onClose回调里无法使用Gateway::getUidByClientId()接口来获得uid，解决办法是在Gateway::bindUid()时记录一个$_SESSION['uid']，onClose的时候用$_SESSION['uid']来获得uid。
     * 注意：断网断电等极端情况可能无法及时触发onClose回调，因为这种情况客户端来不及给服务端发送断开连接的包(fin包)，服务端就无法得知连接已经断开。检测这种极端情况需要心跳检测，并且必须设置$gateway->pingNotResponseLimit>0。这种断网断电的极端情况onClose将被延迟触发，延迟时间为小于$gateway->pingInterval*$gateway->pingNotResponseLimit秒，如果$gateway->pingInterval 和 $gateway->pingNotResponseLimit 中任何一个为0，则可能会无限延迟。
     * @param $client_id 全局唯一的client_id
     */
    public static function onClose($client_id)
    {
    }
}