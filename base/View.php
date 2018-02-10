<?php
namespace fastphp\php;

/**
 *  视图基类
 */
class View
{
    protected $variables = array();
    protected $_controller;
    protected $_action;

    function __construct($controller,$action)
    {
        $this->_controller = strtolower($controller);
        $this->_action = strtolower($action);
    }
    //分配变量
    public function assign($name,$value)
    {
        $this->variables[$name] = $value;
    }
}