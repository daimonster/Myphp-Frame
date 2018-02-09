<?php
/**
 * Mdoel 基类设计到3个类:Model基类本身,它的父类SQL,以及提供数据库连接句柄的Db类
 */
namespace fastphp\base;
use fast\db\sql;
class Model extends Sql
{
    protected $model;
    public function __construct()
    {
        // 获取数据库表名
        if(!$this->table)
        {
            // 获取模型类名称
            $this->model = get_class($this);
            // 删除类名最后的Model字符
            $this->model = substr($this->model,0,-5);
            // 数据库表名与类名一致
            $this->table = strtolower($this->model);
        }
    }
}