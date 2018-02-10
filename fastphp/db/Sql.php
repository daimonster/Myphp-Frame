<?php
namespace fastphp\db;

use \PDOStatement;

class Sql
{
    // 数据库表名
    protected $table;
    // 数据库主键
    protected $primary = 'id';
    // WHERE和ORDER拼装后的条件
    private $filter = '';
    //Pdo bindParam()绑定的参数集合
    private $param = array();
    /**
     * 查询条件拼接，使用方式：
     *
     * $this->where(['id = 1','and title="Web"', ...])->fetch();
     * 为防止注入，建议通过$param方式传入参数：
     * $this->where(['id = :id'], [':id' => $id])->fetch();
     *
     * @param array $where 条件
     * @return $this 当前对象
     */
    public function where($where = array(),$param = array())
    {
        if($where)
        {
            $this->filter .= 'WHERE';//.=把右边的字符串加到左边
            $this->filter .= implode(' ', $where);
            $this->param = $param;
        }
        return $this;
    }
    /**
     * 拼装排序条件，使用方式：
     *
     * $this->order(['id DESC', 'title ASC', ...])->fetch();
     *
     * @param array $order 排序条件
     * @return $this
     */
    public function order($order = array())
    {
        if($order)
        {
            $this->filter .= ' ORDER BY ';
            $this->filter .= implode(',',$order);
        }
        return $this;
    }
    // 查询所有的
    public function fetchAll()
    {
        $sql = sprinft("select * from `%s` %s",$this->table,$this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth,$this->param);

        $sth->execute();
        return $sth->fetch();
    }

    //根据条件(id)删除
    public function delete()
    {
        $sql = sprintf("delete from `%s` where `%s` = :%s",$this->table,$this->primary,$this->primary);
        $sth = Db::pado()->prepare($sql);
        $sth = $this->formatParam($sth,[$this->primary => $id]);
        $sth->execute();
        return $sth->rowCount();
    }
    //新增数据
    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s",$this->table,$this->formatInsert($data));
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth,$data);
        $sth = $this->formatParam($sth,$this->param);
        $sth->execute();

        return $sth->rowCount();
    }
    //修改数据
    public function update()
    {
        $sql = sprintf("update `%s` set %s %s",$this->table,$this->formatUpdate($data),$this->filter);
        $sth = Db::pdo()->prepare($sql);
        $sth = $this->formatParam($sth,$data);
        $sth = $this->formatParam($sth,$this->param);
        $sth->execute();
        return $sth->rowCount();
    }
    /**
     * 占位符绑定具体的变量值
     * @param PDOStatement $sth 要绑定的PDOStatement对象
     * @param array $params 参数，有三种类型：
     * 1）如果SQL语句用问号?占位符，那么$params应该为
     *    [$a, $b, $c]
     * 2）如果SQL语句用冒号:占位符，那么$params应该为
     *    ['a' => $a, 'b' => $b, 'c' => $c]
     *    或者
     *    [':a' => $a, ':b' => $b, ':c' => $c]
     *
     * @return PDOStatement
     */
    public function formatParam(PDOStatement $sth, $params = array())
    {
        foreach($params as $param => &$value )
        {
            $param = is_int($param) ? $param + 1: ":".ltrim($param,':');
            $sth->bindParam($param.$value);
        }
        return $sth;
    }
    // 将数组转换成插入格式的sql语句
    private function formatInsert($data)
    {
        $fields = array();
        $names = array();
        foreach ($data as $key => $value)
        {
            $fileds[] = sprintf("`%s`",$key);
            $names[] = sprintf(":%s",$key);
        }
        $fields = implode(',',$fields);
        $names = implode(',',$names);
        return sprintf("(%s) values (%s)",$field,$name);
    }
    // 将格式转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fileds = array();
        foreach($data as $key => $value)
        {
            $fields[] = sprintf("`%s` = :%s",$key,$key);
        }
        return implode(',',$fields);
    }
}
/**
 * Sql基类是框架的核心部分.为什么?
 * 因为通过它,我们创建了一个SQL抽象层,可以大大减少可数据库的编程工作.
 * 虽然PDO借口本来已经很简洁,但是抽象之后的框架的灵活性更高.
 * Sql类里面有用到Db:PDO()方法没这事我们创建的Db类,它提供一个PDO单例.
 */