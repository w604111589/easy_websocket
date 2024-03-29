<?php

namespace App\WebSocket;

use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

/**
 * Class Index
 *
 * 此类是默认的 websocket 消息解析后访问的 控制器
 *
 * @package App\WebSocket
 */
class Index extends Controller
{
    function hello()
    {
        $this->response()->setMessage('call hello with arg:' . json_encode($this->caller()->getArgs()));
    }

    public function who()
    {
        $this->response()->setMessage('your fd is ' . $this->caller()->getClient()->getFd());
    }

    function delay()
    {
        $this->response()->setMessage('this is delay action');
        $client = $this->caller()->getClient();

        // 异步推送, 这里直接 use fd也是可以的
        TaskManager::getInstance()->async(function () use ($client) {
            $server = ServerManager::getInstance()->getSwooleServer();
            $i = 0;
            while ($i < 5) {
                sleep(1);
                $server->push($client->getFd(), 'push in http at ' . date('H:i:s'));
                $i++;
            }
        });
    }
    /**
     * 消息群发
     */
    function broadcast()
    {
        $server = ServerManager::getInstance()->getSwooleServer();
        $start_fd = 0;

        while (true) {
            $conn_list = $server->getClientList($start_fd, 10);
            if ($conn_list === false or count($conn_list) === 0) {
                echo "finish\n";
                break;
            }
            $start_fd = end($conn_list);
            
            foreach ($conn_list as $fd) {
                $server->push($fd, "broadcast");
            }
        }
    }
}
