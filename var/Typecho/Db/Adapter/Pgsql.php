<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

/**
 * 数据库Pgsql适配器
 *
 * @package Db
 */
class Typecho_Db_Adapter_Pgsql implements Typecho_Db_Adapter
{
    /**
     * 最后一次操作的数据表
     *
     * @access protected
     * @var string
     */
    protected $_lastTable;
    /**
     * 数据库连接字符串标示
     *
     * @access private
     * @var resource
     */
    private $_dbLink;

    /**
     * 判断适配器是否可用
     *
     * @access public
     * @return boolean
     */
    public static function isAvailable()
    {
        return function_exists('pg_connect');
    }

    /**
     * 数据库连接函数
     *
     * @param Typecho_Config $config 数据库配置
     * @return resource
     * @throws Typecho_Db_Exception
     */
    public function connect(Typecho_Config $config)
    {
        if ($this->_dbLink = @pg_connect("host={$config->host} port={$config->port} dbname={$config->database} user={$config->user} password={$config->password}")) {
            if ($config->charset) {
                pg_query($this->_dbLink, "SET NAMES '{$config->charset}'");
            }
            return $this->_dbLink;
        }

        /** 数据库异常 */
        throw new Typecho_Db_Adapter_Exception(@pg_last_error($this->_dbLink));
    }

    /**
     * 获取数据库版本
     *
     * @param mixed $handle
     * @return string
     */
    public function getVersion($handle)
    {
        $version = pg_version($handle);
        return 'pgsql:pgsql ' . $version['server'];
    }

    /**
     * 清空数据表
     *
     * @param string $table
     * @param mixed $handle 连接对象
     * @return mixed|void
     * @throws Typecho_Db_Exception
     */
    public function truncate($table, $handle)
    {
        $this->query('TRUNCATE TABLE ' . $this->quoteColumn($table) . ' RESTART IDENTITY', $handle);
    }

    /**
     * 执行数据库查询
     *
     * @param string $query 数据库查询SQL字符串
     * @param mixed $handle 连接对象
     * @param integer $op 数据库读写状态
     * @param string $action 数据库动作
     * @param string $table 数据表
     * @return resource
     * @throws Typecho_Db_Exception
     */
    public function query($query, $handle, $op = Typecho_Db::READ, $action = null, $table = null)
    {
        $this->_lastTable = $table;
        if ($resource = @pg_query($handle, $query)) {
            return $resource;
        }

        /** 数据库异常 */
        throw new Typecho_Db_Query_Exception(@pg_last_error($this->_dbLink),
            pg_result_error_field(pg_get_result($this->_dbLink), PGSQL_DIAG_SQLSTATE));
    }

    /**
     * 对象引号过滤
     *
     * @access public
     * @param string $string
     * @return string
     */
    public function quoteColumn($string)
    {
        return '"' . $string . '"';
    }

    /**
     * 将数据查询的其中一行作为数组取出,其中字段名对应数组键值
     *
     * @param resource $resource 查询返回资源标识
     * @return array
     */
    public function fetch($resource)
    {
        return pg_fetch_assoc($resource);
    }

    /**
     * 将数据查询的其中一行作为对象取出,其中字段名对应对象属性
     *
     * @param resource $resource 查询的资源数据
     * @return object
     */
    public function fetchObject($resource)
    {
        return pg_fetch_object($resource);
    }

    /**
     * 合成查询语句
     *
     * @access public
     * @param array $sql 查询对象词法数组
     * @return string
     */
    public function parseSelect(array $sql)
    {
        if (!empty($sql['join'])) {
            foreach ($sql['join'] as $val) {
                [$table, $condition, $op] = $val;
                $sql['table'] = "{$sql['table']} {$op} JOIN {$table} ON {$condition}";
            }
        }

        $sql['limit'] = (0 == strlen($sql['limit'])) ? null : ' LIMIT ' . $sql['limit'];
        $sql['offset'] = (0 == strlen($sql['offset'])) ? null : ' OFFSET ' . $sql['offset'];

        return 'SELECT ' . $sql['fields'] . ' FROM ' . $sql['table'] .
            $sql['where'] . $sql['group'] . $sql['having'] . $sql['order'] . $sql['limit'] . $sql['offset'];
    }

    /**
     * 取出最后一次查询影响的行数
     *
     * @param resource $resource 查询的资源数据
     * @param mixed $handle 连接对象
     * @return integer
     */
    public function affectedRows($resource, $handle)
    {
        return pg_affected_rows($resource);
    }

    /**
     * 取出最后一次插入返回的主键值
     *
     * @param resource $resource 查询的资源数据
     * @param mixed $handle 连接对象
     * @return integer
     */
    public function lastInsertId($resource, $handle)
    {
        /** 查看是否存在序列,可能需要更严格的检查 */
        if (pg_fetch_assoc(pg_query($handle, 'SELECT oid FROM pg_class WHERE relname = ' . $this->quoteValue($this->_lastTable . '_seq')))) {
            return pg_fetch_result(pg_query($handle, 'SELECT CURRVAL(' . $this->quoteValue($this->_lastTable . '_seq') . ')'), 0, 0);
        }

        return 0;
    }

    /**
     * 引号转义函数
     *
     * @param string $string 需要转义的字符串
     * @return string
     */
    public function quoteValue($string)
    {
        return '\'' . pg_escape_string($string) . '\'';
    }
}
