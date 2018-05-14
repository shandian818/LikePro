<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/5/14
 * Time: 22:05
 */

namespace likephp;


class Command
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    public function handle()
    {
        global $argv;
        $act_name = isset($argv[1]) ? strtolower($argv[1]) : null;
        $app_name = isset($argv[2]) ? strtolower($argv[2]) : null;
        $allow_act_list = ['start', 'stop'];
        $apps_config = Config::getInstance()->get();
        $allow_app_list = array_keys($apps_config);
        if (!in_array($act_name, $allow_act_list) || !in_array($app_name, $allow_app_list)) {
            $this->help($allow_act_list, $allow_app_list);
        }
        //待完善...
        //待完善...
        //待完善...
        $this->$act_name($app_name);
    }

    private function help($allow_act_list, $allow_app_list)
    {
        $help_string = '';
        $allow_action_cmd_string = implode('|', $allow_act_list);
        $allow_server_cmd_string = implode('|', $allow_app_list);
        $help_string .= '=====欢迎使用LikePHP服务=====' . PHP_EOL;
        $help_string .= '支持命令: ' . $allow_action_cmd_string . PHP_EOL;
        $help_string .= '支持应用: ' . $allow_server_cmd_string . PHP_EOL;
        $help_string .= '例子如下:' . PHP_EOL;
        $help_string .= 'php likephp [ ' . $allow_action_cmd_string . ' ] [ ' . $allow_server_cmd_string . ' ]:' . PHP_EOL;
        $help_string .= '例如:php likephp ' . $allow_act_list[0] . ' ' . $allow_app_list[0] . PHP_EOL;
        die($help_string);
    }

    private function start($app_name)
    {
        //待完善...
        //待完善...
        //待完善...
        $config = Config::getInstance()->get($app_name . '.server');
        $callback_name = '\\likephp\\event\\' . ucfirst($config['type']).'Event';
        $server = new Server($config, $callback_name);
        $server->run();
    }
}