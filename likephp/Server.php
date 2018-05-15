<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/5/14
 * Time: 22:23
 */

namespace likephp;


class Server
{
    private static $instance;

    private $server;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->checkSever();

    }

    private function checkSever()
    {
        $swoole_version_limit = '2.0.0';
        if (php_sapi_name() != 'cli') {
            Clog::getInstance()->error('server必须在CLI模式下运行');
            die();
        }
        //2.校验swoole扩展
        if (!extension_loaded('swoole')) {
            Clog::getInstance()->error('swoole扩展未安装');
            die();
        }
        //3.校验swoole版本
        $swoole_version = swoole_version();
        if (version_compare($swoole_version, $swoole_version_limit, '<')) {
            Clog::getInstance()->error('swoole版本(' . $swoole_version . ')必须大于' . $swoole_version_limit);
            die();
        }
    }

    private function initServer($config = [], $callback_name = '')
    {
        switch (strtolower($config['type'])) {
            case 'http':
                $this->server = new \swoole_http_server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
                $event_list = [
                    'Request'
                ];
                break;
            case 'ws':
                $this->server = new \swoole_websocket_server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
                $event_list = [
                    'Request',
                    'Open',
                    'Message',
                    'Close',
                    'HandShake'
                ];
                break;
            case 'tcp':
                $this->server = new \swoole_server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
                $event_list = [
                    'Start',
                    'ManagerStart',
                    'ManagerStop',
                    'PipeMessage',
                    'Task',
                    'Packet',
                    'Finish',
                    'Receive',
                    'Connect',
                    'Close',
                    'Timer',
                    'WorkerStart',
                    'WorkerStop',
                    'Shutdown',
                    'WorkerError'
                ];
                break;
            default:
                Clog::getInstance()->error($config['type'] . '不支持');
                die();
        }
        if (isset($config['setting']) && !empty($config['setting'])) {
            $this->server->set($config['setting']);
        }
        if (!class_exists($callback_name)) {
            Clog::getInstance()->error('服务回调不存在' . $callback_name);
            die();
        }
        foreach ($event_list as $event) {
            if (method_exists($callback_name, 'on' . $event)) {
                $this->server->on($event, [$callback_name, 'on' . $event]);
            }
        }
    }

    public function run($config = [], $callback_name = '')
    {
        $this->initServer($config, $callback_name);
        $this->server->start();
    }
}