<?php


namespace App\Timer;

use \EasySwoole\Component\Timer;


class Broadcast
{
    /**
     * 每5秒向每次websocket连接发送一条广播消息
     * @param $server websocket服务
     * 
     * @return void
     */
    public static function setTimerForBroadcastPerFive(\swoole_websocket_server $server)
    {
        //时间间隔是以毫秒为单位
        Timer::getInstance()->loop(5 * 1000, function () use ($server) {
            // $server = ServerManager::getInstance()->getSwooleServer();
            $start_fd = 0;
            while (true) {
                $conn_list = $server->getClientList($start_fd, 10);
                if ($conn_list === false or count($conn_list) === 0) {
                    echo "finish2\n";
                    break;
                }
                $start_fd = end($conn_list);

                foreach ($conn_list as $fd) {
                    $server->push($fd, "broadcast2");
                }
            }
        });
    }
}
