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
        call_user_func_array(array($dispatch));
    }
    
    // 检测开发环境
    public function setReporting()
    {
        if(APP_DEBUG === true)
        {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        }
        else
        {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors','On');
        }
    }

    // 删除敏感字符
    public function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map(array($this,'stripSlashesDeep'),$value) : stripslashes($value);
        return $value;
    }

    //检测敏感字符病删除
    public function removeMagicQuotes()
    {
        if (get_magic_quotes_gpc()) 
        {
            $_GET = isset($_GET) ? $this->stripSlashesDeep($_GET ) : '';
            $_POST = isset($_POST) ? $this->stripSlashesDeep($_POST ) : '';
            $_COOKIE = isset($_COOKIE) ? $this->stripSlashesDeep($_COOKIE) : '';
            $_SESSION = isset($_SESSION) ? $this->stripSlashesDeep($_SESSION) : '';
        }
    }

    /**
     * 检测自定义全局变量并移除.因为resgister_globals已经弃用,如果已经弃用的register_
     * globals指令被设置为on,那么局部变量也将在脚本的全局作用域中可用.
     */
    public function unregisterGlobal()
    {
        if(ini_get('getgister_globals'))
        {
            $array = array('_SESSION','_POST','_GET','_COOKIE','_REQUEST','_SERVER','_ENV','_FILES');
                foreach($array as $value)
                {
                    foreach($GLOBALS[$value] as $key => $var)
                    {
                        if($var === $GLOBALS[key])
                        {
                            unset($GLOBALS[$key]);
                        }
                    }
                }
        }
    }

    // 配置数据库信息
    public function setDbConfig()
    {
        if($this->config['db'])
        {
            define('DB_HOST',$this->config['db']['host']);
            define('DB_NAME',$this->config['db']['dbname']);
            define('DB_USER',$this->config['db']['username']);
            define('DB_PASS',$this->config['db']['password']);
        }
    }

    // 自动加载类
    public function loadClass($className)
    {
        $classMap = $this->classMap();
        if(isset($classMap[$className]))
        {
            $file = $classMap[$className];
        }
        elseif(strops($className,'\\') !== false)
        {
            // 包含应用(applications目录)文件
            $file = APP_PATH.str_replace('\\','/',$className).'.php';
            if(!is_file($file))
            {
                return;
            }
        }
        else
        {
            return;
        }
        include $file;
        //这里可以加入判断,如果名为$className的类、接口护着性状不存在，则在调试模式下抛出错误
    }
    
    // 内核文件明明文件映射关系
    protected function classMap()
    {
        return 
        [
            'fastphp\base\Controller' => CORE_PATH . '/base/Controller.php',
            'fastphp\base\Model' => CORE_PATH . '/base/Model.php',
            'fastphp\base\View' => CORE_PATH . '/base/View.php',
            'fastphp\db\Db' => CORE_PATH . '/db/Db.php',
            'fastphp\db\Sql' => CORE_PATH . '/db/Sql.php',
        ];
    }
}
/**
 * 讲解主请求方法route(),它也称路由方法.
 * 路由方法的主要作用是:截取URL,并解析出控制器名、方法名和URL参数
 * 假设我们的URL是这样的：
 * yoursite.com/controllerName/actionName/queryString
 * 当浏览器访问上面的URL，route()从全局额变量$_SERVER['REQUEST_URL']中获取到字符串/controllerName/actionName/queryString。
 * 然后，会将这个字符串分割城三个备份：controllerName、actionName和queryString
 * 例如，URL链接为：yoursite.com/item/detail/1/hello，那么route()分割之后，

 *  ControllerName名就是：item
 *  actionName名就是：detail
 *  URL参数就是：array(1, hello)

 * 分割完成后，路由方法再实例化控制器：itemController，并调用其中的detail方法 。
 */
