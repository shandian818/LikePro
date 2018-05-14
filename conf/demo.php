<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/5/14
 * Time: 22:34
 */

return [
    'server' => [
        'type' => 'http',
        'host' => '0.0.0.0',
        'port' => 9501,
        'mode' => SWOOLE_PROCESS,
        'sock_type' => SWOOLE_SOCK_TCP,
        'setting' => [
            'daemonize' => 1,//守护进程
            'upload_tmp_dir' => WEB_PATH . 'upload/',
            'enable_static_handler' => true,
            'document_root' => WEB_PATH,
            'log_file' => RUNTIME_PATH . 'http.log',
            'pid_file' => RUNTIME_PATH . 'http.pid',
        ]
    ]
];