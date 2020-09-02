<?php

namespace App\Console\Commands;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class WorkerMan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workman {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a Workerman server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;
        $action = $this->argument('action');

        $argv[0] = 'wk';
        $argv[1] = $action;
        $argv[2] = $this->option('d') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }
    //BusinessWorker是运行业务逻辑的进程，BusinessWorker收到Gateway转发来的事件及请求时会默认调用Events.php中的onConnect onMessage
    private function startBusinessWorker()
    {
        $worker                  = new BusinessWorker();
        $worker->name            = 'BusinessWorker';        //设置BusinessWorker进程的名称
        $worker->count           = 1;       //设置BusinessWorker进程的数量
        $worker->registerAddress = '127.0.0.1:2018';        //注册服务地址-向Register进程注册-内部通讯使用
        $worker->eventHandler    = \App\Workerman\Events::class;        //设置使用哪个类来处理业务,业务类至少要实现onMessage静态方法，onConnect和onClose静态方法可以不用实现
    }

    private function startGateWay()
    {
        // 证书最好是申请的证书-开启wss 打开注释
        /**
         * Workerman版本不小于3.3.7
         * PHP安装了openssl扩展
         * 已经申请了证书（pem/crt文件及key文件）放在磁盘某个目录(位置任意)
        $context = array(
        // 更多ssl选项请参考手册 http://php.net/manual/zh/context.ssl.php
        'ssl' => array(
        // 请使用绝对路径
        'local_cert'                 => '磁盘路径/server.pem', // 也可以是crt文件
        'local_pk'                   => '磁盘路径/server.key',
        'verify_peer'               => false,
        // 'allow_self_signed' => true, //如果是自签名证书需要开启此选项
        )
        );
         */
        $gateway = new Gateway("websocket://0.0.0.0:2019");
        // 开启SSL，websocket+SSL 即wss websocket协议(端口任意，只要没有被其它程序占用就行)
        //  $gateway = new Gateway("websocket://0.0.0.0:443", $context);
        //  $gateway->transport = 'ssl';
        $gateway->name                 = 'Gateway';     //设置Gateway进程的名称，方便status命令中查看统计
        $gateway->count                = 1;     //进程的数量
        $gateway->lanIp                = '127.0.0.1';       //内网ip,多服务器分布式部署的时候需要填写真实的内网ip
        $gateway->startPort            = 2010;      //监听本机端口的起始端口
        $gateway->pingInterval         = 30;        //心跳检测时间间隔 单位：秒。如果设置为0代表不做任何心跳检测。
        $gateway->pingNotResponseLimit = 0;     //客户端连续$pingNotResponseLimit次$pingInterval时间内不发送任何数据则断开链接，并触发onClose。 如果设置为0代表客户端不用发送心跳数据

        $gateway->pingData             = '{"type":"@heart@"}';      //当需要服务端定时给客户端发送心跳数据时， $gateway->pingData设置为服务端要发送的心跳请求数据，心跳数据是任意的，只要客户端能识别即可。
        $gateway->registerAddress      = '127.0.0.1:2018';      //注册服务地址-向Register进程注册-内部通讯使用
    }

    /**
     * Gateway进程和BusinessWorker进程启动后分别向Register进程注册自己的通讯地址，Gateway进程和BusinessWorker通过Register进程得到通讯地址后，就可以建立起连接并通讯了。
     */
    private function startRegister()
    {
        new Register('text://0.0.0.0:2018');
    }
}
