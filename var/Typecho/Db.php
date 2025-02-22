<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Db.php 107 2008-04-11 07:14:43Z magike.net $
 */

/**
 * 包含获取数据支持方法的类.
 * 必须定义__TYPECHO_DB_HOST__, __TYPECHO_DB_PORT__, __TYPECHO_DB_NAME__,
 * __TYPECHO_DB_USER__, __TYPECHO_DB_PASS__, __TYPECHO_DB_CHAR__
 *
 * @package Db
 */
class Typecho_Db
{
    /** 读取数据库 */
    const READ = 1;

    /** 写入数据库 */
    const WRITE = 2;

    /** 升序方式 */
    const SORT_ASC = 'ASC';

    /** 降序方式 */
    const SORT_DESC = 'DESC';

    /** 表内连接方式 */
    const INNER_JOIN = 'INNER';

    /** 表外连接方式 */
    const OUTER_JOIN = 'OUTER';

    /** 表左连接方式 */
    const LEFT_JOIN = 'LEFT';

    /** 表右连接方式 */
    const RIGHT_JOIN = 'RIGHT';

    /** 数据库查询操作 */
    const SELECT = 'SELECT';

    /** 数据库更新操作 */
    const UPDATE = 'UPDATE';

    /** 数据库插入操作 */
    const INSERT = 'INSERT';

    /** 数据库删除操作 */
    const DELETE = 'DELETE';

    /**
     * 数据库适配器
     * @var Typecho_Db_Adapter
     */
    private $_adapter;

    /**
     * 默认配置
     *
     * @var array
     */
    private $_config;

    /**
     * 已经连接
     *
     * @access private
     * @var array
     */
    private $_connectedPool;

    /**
     * 前缀
     *
     * @access private
     * @var string
     */
    private $_prefix;

    /**
     * 适配器名称
     *
     * @access private
     * @var string
     */
    private $_adapterName;

    /**
     * 实例化的数据库对象
     * @var Typecho_Db
     */
    private static $_instance;

    /**
     * 数据库类构造函数
     *
     * @param mixed $adapterName 适配器名称
     * @param string $prefix 前缀
     *
     * @throws Typecho_Db_Exception
     */
    public function __construct($adapterName, string $prefix = 'typecho_')
    {
        /** 获取适配器名称 */
        $this->_adapterName = $adapterName;

        /** 数据库适配器 */
        $adapterName = 'Typecho_Db_Adapter_' . $adapterName;

        if (!call_user_func([$adapterName, 'isAvailable'])) {
            throw new Typecho_Db_Exception("Adapter {$adapterName} is not available");
        }

        $this->_prefix = $prefix;

        /** 初始化内部变量 */
        $this->_connectedPool = [];

        $this->_config = [
            self::READ => [],
            self::WRITE => []
        ];

        //实例化适配器对象
        $this->_adapter = new $adapterName();
    }

    /**
     * 获取适配器名称
     *
     * @access public
     * @return string
     */
    public function getAdapterName(): string
    {
        return $this->_adapterName;
    }

    /**
     * 获取表前缀
     *
     * @access public
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->_prefix;
    }

    /**
     * @param Typecho_Config $config
     * @param int $op
     */
    public function addConfig(Typecho_Config $config, int $op)
    {
        if ($op & self::READ) {
            $this->_config[self::READ][] = $config;
        }

        if ($op & self::WRITE) {
            $this->_config[self::WRITE][] = $config;
        }
    }

    /**
     * getConfig
     *
     * @param int $op
     *
     * @return Typecho_Config
     * @throws Typecho_Db_Exception
     */
    public function getConfig(int $op): Typecho_Config
    {
        if (empty($this->_config[$op])) {
            /** Typecho_Db_Exception */
            throw new Typecho_Db_Exception('Missing Database Connection');
        }

        $key = array_rand($this->_config[$op]);
        return $this->_config[$op][$key];
    }

    /**
     * 重置连接池
     *
     * @return void
     */
    public function flushPool()
    {
        $this->_connectedPool = [];
    }

    /**
     * 选择数据库
     *
     * @param int $op
     *
     * @return mixed
     * @throws Typecho_Db_Exception
     */
    public function selectDb(int $op)
    {
        if (!isset($this->_connectedPool[$op])) {
            $selectConnectionConfig = $this->getConfig($op);
            $selectConnectionHandle = $this->_adapter->connect($selectConnectionConfig);
            $this->_connectedPool[$op] = $selectConnectionHandle;
        }

        return $this->_connectedPool[$op];
    }

    /**
     * 获取SQL词法构建器实例化对象
     *
     * @return Typecho_Db_Query
     */
    public function sql(): Typecho_Db_Query
    {
        return new Typecho_Db_Query($this->_adapter, $this->_prefix);
    }

    /**
     * 为多数据库提供支持
     *
     * @access public
     * @param array $config 数据库实例
     * @param integer $op 数据库操作
     * @return void
     */
    public function addServer(array $config, int $op)
    {
        $this->addConfig(Typecho_Config::factory($config), $op);
        $this->flushPool();
    }

    /**
     * 获取版本
     *
     * @param int $op
     *
     * @return string
     * @throws Typecho_Db_Exception
     */
    public function getVersion(int $op = self::READ): string
    {
        return $this->_adapter->getVersion($this->selectDb($op));
    }

    /**
     * 设置默认数据库对象
     *
     * @access public
     * @param Typecho_Db $db 数据库对象
     * @return void
     */
    public static function set(Typecho_Db $db)
    {
        self::$_instance = $db;
    }

    /**
     * 获取数据库实例化对象
     * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
     *
     * @return Typecho_Db
     * @throws Typecho_Db_Exception
     */
    public static function get(): Typecho_Db
    {
        if (empty(self::$_instance)) {
            /** Typecho_Db_Exception */
            throw new Typecho_Db_Exception('Missing Database Object');
        }

        return self::$_instance;
    }

    /**
     * 选择查询字段
     *
     * @param ...$ags
     *
     * @return Typecho_Db_Query
     * @throws Typecho_Db_Exception
     */
    public function select(...$ags): Typecho_Db_Query
    {
        $this->selectDb(self::READ);

        $args = func_get_args();
        return call_user_func_array([$this->sql(), 'select'], $args ?: ['*']);
    }

    /**
     * 更新记录操作(UPDATE)
     *
     * @param string $table 需要更新记录的表
     *
     * @return Typecho_Db_Query
     * @throws Typecho_Db_Exception
     */
    public function update(string $table): Typecho_Db_Query
    {
        $this->selectDb(self::WRITE);

        return $this->sql()->update($table);
    }

    /**
     * 删除记录操作(DELETE)
     *
     * @param string $table 需要删除记录的表
     *
     * @return Typecho_Db_Query
     * @throws Typecho_Db_Exception
     */
    public function delete(string $table): Typecho_Db_Query
    {
        $this->selectDb(self::WRITE);

        return $this->sql()->delete($table);
    }

    /**
     * 插入记录操作(INSERT)
     *
     * @param string $table 需要插入记录的表
     *
     * @return Typecho_Db_Query
     * @throws Typecho_Db_Exception
     */
    public function insert(string $table): Typecho_Db_Query
    {
        $this->selectDb(self::WRITE);

        return $this->sql()->insert($table);
    }

    /**
     * @param $table
     * @throws Typecho_Db_Exception
     */
    public function truncate($table)
    {
        $table = preg_replace("/^table\./", $this->_prefix, $table);
        $this->_adapter->truncate($table, $this->selectDb(self::WRITE));
    }

    /**
     * 执行查询语句
     *
     * @param mixed $query 查询语句或者查询对象
     * @param int $op 数据库读写状态
     * @param string $action 操作动作
     *
     * @return mixed
     * @throws Typecho_Db_Exception
     */
    public function query($query, int $op = self::READ, string $action = self::SELECT)
    {
        $table = null;

        /** 在适配器中执行查询 */
        if ($query instanceof Typecho_Db_Query) {
            $action = $query->getAttribute('action');
            $table = $query->getAttribute('table');
            $op = (self::UPDATE == $action || self::DELETE == $action
                || self::INSERT == $action) ? self::WRITE : self::READ;
        } else if (!is_string($query)) {
            /** 如果query不是对象也不是字符串,那么将其判断为查询资源句柄,直接返回 */
            return $query;
        }

        /** 选择连接池 */
        $handle = $this->selectDb($op);

        /** 提交查询 */
        $resource = $this->_adapter->query($query instanceof Typecho_Db_Query ?
            $query->prepare($query) : $query, $handle, $op, $action, $table);

        if ($action) {
            //根据查询动作返回相应资源
            switch ($action) {
                case self::UPDATE:
                case self::DELETE:
                    return $this->_adapter->affectedRows($resource, $handle);
                case self::INSERT:
                    return $this->_adapter->lastInsertId($resource, $handle);
                case self::SELECT:
                default:
                    return $resource;
            }
        } else {
            //如果直接执行查询语句则返回资源
            return $resource;
        }
    }

    /**
     * 一次取出所有行
     *
     * @param mixed $query 查询对象
     * @param array|null $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     *
     * @return array
     * @throws Typecho_Db_Exception
     */
    public function fetchAll($query, ?array $filter = null): array
    {
        //执行查询
        $resource = $this->query($query, self::READ);
        $result = [];

        /** 取出过滤器 */
        if (!empty($filter)) {
            [$object, $method] = $filter;
        }

        //取出每一行
        while ($rows = $this->_adapter->fetch($resource)) {
            //判断是否有过滤器
            $result[] = $filter ? call_user_func([&$object, $method], $rows) : $rows;
        }

        return $result;
    }

    /**
     * 一次取出一行
     *
     * @param mixed $query 查询对象
     * @param array|null $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     *
     * @return mixed
     * @throws Typecho_Db_Exception
     */
    public function fetchRow($query, ?array $filter = null)
    {
        $resource = $this->query($query, self::READ);

        /** 取出过滤器 */
        if ($filter) {
            [$object, $method] = $filter;
        }

        return ($rows = $this->_adapter->fetch($resource)) ?
            ($filter ? $object->$method($rows) : $rows) :
            [];
    }

    /**
     * 一次取出一个对象
     *
     * @param mixed $query 查询对象
     * @param array|null $filter 行过滤器函数,将查询的每一行作为第一个参数传入指定的过滤器中
     *
     * @return mixed
     * @throws Typecho_Db_Exception
     */
    public function fetchObject($query, ?array $filter = null)
    {
        $resource = $this->query($query, self::READ);

        /** 取出过滤器 */
        if ($filter) {
            [$object, $method] = $filter;
        }

        return ($rows = $this->_adapter->fetchObject($resource)) ?
            ($filter ? $object->$method($rows) : $rows) :
            new stdClass();
    }
}
