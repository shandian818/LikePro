<?php
/**
 * Created by PhpStorm.
 * User: jiang
 * Date: 2018/5/14
 * Time: 21:54
 */

namespace likephp;


class LikePHP
{
    const VERSION = '0.1';

    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    private function __construct()
    {
        //1.校验运行环境
        $this->checkEnv();
        //2.初始化
        $this->init();
        //3.加载公共函数
        $this->loadFunc();
        //4.注册自动加载
        $this->registerAutoLoad();
        //5.加载配置
        $this->loadConf();
        //6.注册异常
        $this->registerError();
    }

    /**
     * 校验运行环境
     */
    private function checkEnv()
    {
        //校验php版本
        $php_version_limit = '7.0.0';
        if (version_compare(PHP_VERSION, $php_version_limit, '<')) {
            Clog::getInstance()->error('php版本(' . PHP_VERSION . ')必须大于' . $php_version_limit);
            die();
        }
    }

    /**
     * 初始化
     */
    private function init()
    {
        defined('LIKE_PATH') or define('LIKE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
        defined('ROOT_PATH') or define('ROOT_PATH', realpath(LIKE_PATH . '/../') . DIRECTORY_SEPARATOR);
        defined('APPS_PATH') or define('APPS_PATH', ROOT_PATH . 'apps' . DIRECTORY_SEPARATOR);
        defined('CONF_PATH') or define('CONF_PATH', ROOT_PATH . 'conf' . DIRECTORY_SEPARATOR);
        defined('FUNC_PATH') or define('FUNC_PATH', ROOT_PATH . 'func' . DIRECTORY_SEPARATOR);
        defined('RUNTIME_PATH') or define('RUNTIME_PATH', ROOT_PATH . 'runtime' . DIRECTORY_SEPARATOR);
        defined('WEB_PATH') or define('WEB_PATH', ROOT_PATH . 'web' . DIRECTORY_SEPARATOR);
    }

    /**
     * 加载公共函数
     * @param string $dir
     */
    private function loadFunc($dir = '')
    {
        $dir = rtrim($dir ?: FUNC_PATH, DIRECTORY_SEPARATOR);
        $files = scandir($dir);
        $files = array_diff($files, ['.', '..']);
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    $this->loadFunc($path);
                } elseif (false !== strpos($file, '.php')) {
                    require_once $path;
                }
            }
        }
    }

    /**
     * 加载配置
     * @param string $dir
     */
    private function loadConf($dir = '')
    {
        $dir = rtrim($dir ?: CONF_PATH, DIRECTORY_SEPARATOR);
        $files = scandir($dir);
        $files = array_diff($files, ['.', '..']);
        if (!empty($files)) {
            foreach ($files as $file) {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    $this->loadConf($path);
                } elseif (false !== strpos($file, '.php')) {
                    $filename = strtolower(basename($file, '.php'));
                    @$config_value = require_once $path;
                    Config::getInstance()->set($filename, $config_value);
                }
            }
        }
    }

    /**
     * 自动加载
     */
    private static function registerAutoLoad()
    {
        $name_map = [
            ['likephp', LIKE_PATH],
            ['apps', APPS_PATH]
        ];
        require_once __DIR__ . '/Loader.php';
        Loader::getInstance()->registerNamespaces($name_map);
        //处理composer自动加载
        $composer_autoload_file = ROOT_PATH . 'vendor/autoload.php';
        if (file_exists($composer_autoload_file)) {
            require_once $composer_autoload_file;
        }
    }

    private function registerError()
    {
        Error::getInstance()->register();
    }

    public function run()
    {
        Command::getInstance()->handle();
    }
}