<?php
namespace fastphp;
// 框架根目录
define('CORE_PATH') or define('CORE_PATH',__DIR__);

/**
 * fastphp框架核心
 */
class Fastphp
{
    //配置内容
    protected $config = [];
    public function __construct($config)
    {
        $this->config = $config;
    }
    //运行程序
    public function run()
    {
        spl_autoload_register()
    }
}

  