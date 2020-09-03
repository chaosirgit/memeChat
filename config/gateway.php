<?php


return [
    //BusinessWorker是运行业务逻辑的进程，BusinessWorker收到Gateway转发来的事件及请求时会默认调用Events.php中的onConnect onMessage
    'business' => [
        //注册服务地址-向Register进程注册-内部通讯使用
        'register_address' => env('BUSINESS_REGISTER_ADDRESS', '127.0.0.1:2020'),
        //设置BusinessWorker进程的名称
        'name'             => env('BUSINESS_NAME', 'BusinessWorker'),
        //设置BusinessWorker进程的数量
        'count'            => env('BUSINESS_COUNT', 1),
        //设置使用哪个类来处理业务,业务类至少要实现onMessage静态方法，onConnect和onClose静态方法可以不用实现
        'event'            => env('BUSINESS_EVENT', \App\Workerman\Events::class),
    ],
    'gateway'  => [
        /**
         * Workerman版本不小于3.3.7
         * PHP安装了openssl扩展
         * 已经申请了证书（pem/crt文件及key文件）放在磁盘某个目录(位置任意)
         * 更多ssl选项请参考手册 http://php.net/manual/zh/context.ssl.php
         */

        //默认关闭 SSL,开启 需要配置 ssl内容
        'ssl_open' => env('GATEWAY_SSL', false),
        'ssl'      => [
            // 磁盘路径/server.pem 也可以是crt文件
            'local_cert' => env('GATEWAY_SSL_LOCAL_CERT', ''),
            // 磁盘路径/server.key
            'local_pk'   => env('GATEWAY_SSL_LOCAL_PK', ''),

            'verify_peer'       => env('GATEWAY_VERIFY_PEER', false),
            //如果是自签名证书需要开启此选项
            'allow_self_signed' => env('GATEWAY_SELF_SIGNED', false),
        ],

        'address'          => env('GATEWAY_ADDRESS', 'websocket://0.0.0.0'),
        //websocket协议 端口
        'port'             => env('GATEWAY_PORT', '2021'),
        //开启SSL，websocket+SSL 即wss websocket协议(端口任意，只要没有被其它程序占用就行)
        //'address' => env('GATEWAY_ADDRESS','websocket://0.0.0.0:443')

        //设置Gateway进程的名称，方便status命令中查看统计
        'name'             => env('GATEWAY_NAME', 'Gateway'),
        //进程的数量
        'count'            => env('GATEWAY_COUNT', 1),
        //内网ip,多服务器分布式部署的时候需要填写真实的内网ip
        'lan_ip'           => env('GATEWAY_LAN_IP', '127.0.0.1'),
        //监听本机端口的起始端口
        'start_port'       => env('GATEWAY_START_PORT', 2010),
        //心跳检测时间间隔 单位：秒。如果设置为0代表不做任何心跳检测。
        'heart_ping'       => env('GATEWAY_HEART_PING', 30),
        //客户端连续 ping_not 次 heart_ping 时间内不发送任何数据则断开链接，并触发onClose。 如果设置为0代表客户端不用发送心跳数据
        'ping_not'         => env('GATEWAY_PING_NOT', 0),
        //当需要服务端定时给客户端发送心跳数据时， $gateway->pingData设置为服务端要发送的心跳请求数据，心跳数据是任意的，只要客户端能识别即可。
        'ping_data'        => env('GATEWAY_PING_DATA', '{"type":"@heart@"}'),
        //注册服务地址-向Register进程注册-内部通讯使用
        'register_address' => env('GATEWAY_REGISTER_ADDRESS', '127.0.0.1:2020'),
    ],
    'start'    => [
        //Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址，Gateway进程和BusinessWorker通过Register进程得到通讯地址后，就可以建立起连接并通讯了。
        'address' => env('GATEWAY_START_ADDRESS', 'text://0.0.0.0:2020'),
    ],
    'code_register' => env('GATEWAY_CODE_REGISTER','127.0.0.1:2020'),
];
