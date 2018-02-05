<?php
//它的作用就是保存一些常用的配置,以及配置默认控制器名和操作名
//数据库配置
$config['db']['host'] = 'localhost';
$config['db']['username'] = 'root';
$config['db']['password'] = '123456';
$config['db']['dbname'] = 'project';

// 默认控制器和操作名
$config['db']['defaultCont'] = 'Item';
$config['db']['defaultAction'] = 'Index';

return $config;
//入口中的config变量接受到配置参数后,再传给框架的核心类,也就是Fastphp类
