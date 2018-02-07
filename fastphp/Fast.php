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
        spl_autoload_register(array(this,'loadClass'));
        $this->setReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->route();
    }

    //路由处理
    public function route()
    {
        $controllerName = $this->config['defaultController'];
        $actionName = $this->config['defaultAction'];
        $param = array();

        $url = $_SERVER['REQUEST_URL'];
        //清除?之后的内容
        $position = strpos($url,'?');
        $url = $position === false ? $url : substr($url,0,$position);

        //删除前后的'\'
        $url = trim($url,'/');

        if($url)
        {
            //使用'/'分割字符串,并保存在数组中
            $urlArray = explode('/',$url);
            //删除空的数组元素
            $urlArray = array_filter($urlArray);
            //获取控制器名
            $controllerName = ucfirst($urlArray[0]);//将首字母改成大写字母
            //获取动作名
            array_shift($urlArray);//将数组开头的单元移出数组 
            $actionName = $urlArray ? $urlArray[0] : $actionName;

            // 获取URL参数
            array_shift($urlArray);
            $param = $urlArray ? $urlArray : array();
        }


        //判断控制器和操作是否存在
        $controller = 'app\\controllers\\'.$controllerName.'Controller';
        if(!class_exists($controller))
        {
            exit($controller.'控制器不存在');
        }
        if(!method_exists($controller,$actionName))
        {
            exit($actionName.'方法不存在');
        }

        // 如果控制器和操作名存在,则实例化控制器,因为控制器对象里面
        // 还会用到控制器名和操作名,所以实例化的时候把他们两的名称也传进去.
        // 结合Contoller基类一起看
        $dispatch = new $controller($controllerName,$actionName);
        // $dispatch保存控制器实例化的对象,我们就可以调用它的方法
        // 也可以像方法中传入参数,一下等同于:$dispatch->$actionName($param)
        call_user_func_array(array($dispatch))
    }
}
/**
 * 讲解主请求方法route(),它也称路由方法.
 * 路由方法的主要作用是:截取URL,并解析出控制器名、方法名和URL参数
 * 假设我们的URL是这样的：
 * yoursite.com/controllerName/actionName/queryString
 * 当浏览器访问上面的URL，route()从全局额变量$_SERVER['REQUEST_URL']中获取到字符串/controllerName/actionName/queryString。
 * 然后，会将这个字符串分割城三个备份：controllerName、actionName和queryString
 * 
 */
