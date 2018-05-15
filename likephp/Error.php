<?php
/**
 * Created by PhpStorm.
 * User: Jiangxijun
 * Date: 2018/5/15
 * Time: 14:04
 */

namespace likephp;


class Error
{
    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    public function register()
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }

    public function appError($errno, $errstr, $errfile, $errline, $errcontext)
    {
        ob_start();
        ob_clean();
        $_html = '<b>出错啦!</b>';
        $_html .= '<p>' . $errstr . '</p>';
        $_html .= '<p>' . $errfile . ':' . $errline . '</p>';
        die($_html);
    }

    public function appException($e)
    {
        ob_start();
        ob_clean();
        $_html = '<b>异常啦!</b>';
        $_html .= '<p>' . $e->getMessage() . '</p>';
        $_html .= '<p>' . $e->getFile() . ':' . $e->getLine() . '</p>';
        die($_html);
    }

    public function appShutdown()
    {
        $error = error_get_last();
        if (!is_null($error) && in_array($error['type'], ['E_ERROR', 'E_CORE_ERROR', 'E_COMPILE_ERROR', 'E_RECOVERABLE_ERROR'])) {
            //发生致命错误
            die('发生致命错误');
        }
    }
}