<?php

namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;
use App\WebSocket\WebSocketParser;
use App\Timer\Broadcast;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.
        //设置websocket控制器
        self::setRegister($register);
        //设置服务启动时加载时间
        self::addRegister($register);

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    /**
     * **************** websocket控制器 **********************
     */
    public static function setRegister(EventRegister $register)
    {

        // 创建一个 Dispatcher 配置
        $conf = new \EasySwoole\Socket\Config();
        // 设置 Dispatcher 为 WebSocket 模式
        $conf->setType(\EasySwoole\Socket\Config::WEB_SOCKET);
        //实例化解析器对象
        $webSocketParser = new WebSocketParser();
        // 设置解析器对象
        $conf->setParser($webSocketParser);
        // 创建 Dispatcher 对象 并注入 config 对象
        $dispatch = new Dispatcher($conf);
        // 给server 注册相关事件 在 WebSocket 模式下  on message 事件必须注册 并且交给 Dispatcher 对象处理
        $register->set(EventRegister::onMessage, function (\swoole_websocket_server $server, \swoole_websocket_frame $frame) use ($dispatch) {
            $dispatch->dispatch($server, $frame->data, $frame);
        });
    }

    public static function addRegister(EventRegister $register)
    {
        //添加服务启动时间
        $register->add(EventRegister::onWorkerStart, function (\swoole_websocket_server $server, int $workerId) {
            //如何避免定时器因为进程重启而丢失
            //例如在第一个进程 添加一个10秒的定时器
            if ($workerId == 0) {
                Broadcast::setTimerForBroadcastPerFive($server);

            }
        });
    }
}
