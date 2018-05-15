<?php
/**
 * Created by PhpStorm.
 * User: Jiangxijun
 * Date: 2018/5/15
 * Time: 11:21
 */

namespace likephp;

class Clog
{
    private static $instance;

    private $typeColor = [
        'ERRO' => 31,
        'SUCC' => 36,
        'WARN' => 33,
        'TRAC' => 34,
        'INFO' => 35
    ];

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    public function error($log)
    {
        $string = $this->getString('ERRO', $log);
        echo $string;
    }

    public function success($log)
    {
        $string = $this->getString('SUCC', $log);
        echo $string;
    }

    public function warn($log)
    {
        $string = $this->getString('WARN', $log);
        echo $string;
    }

    public function trace($log)
    {
        $string = $this->getString('TRAC', $log);
        echo $string;
    }

    public function info($log)
    {
        $string = $this->getString('INFO', $log);
        echo $string;
    }

    private function getString($type, $log)
    {
        $string = '';
        if (in_array($type, array_keys($this->typeColor))) {
            $now_time = date('Y-m-d H:i:s');
            $type_color = $this->typeColor[$type];
            $string = "\033[1;{$type_color}m {$now_time} {$type} {$log} \033[0m ".PHP_EOL;
        }
        return $string;
    }
}