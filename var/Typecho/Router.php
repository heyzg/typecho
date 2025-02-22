<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id: Route.php 107 2008-04-11 07:14:43Z magike.net $
 */

/**
 * Typecho组件基类
 *
 * TODO 增加cache缓存
 * @package Router
 */
class Typecho_Router
{
    /**
     * 当前路由名称
     *
     * @access public
     * @var string
     */
    public static $current;

    /**
     * 已经解析完毕的路由表配置
     *
     * @access private
     * @var mixed
     */
    private static $_routingTable = [];

    /**
     * 全路径
     *
     * @access private
     * @var string
     */
    private static $_pathInfo = null;

    /**
     * 解析路径
     *
     * @access public
     *
     * @param string|null $pathInfo 全路径
     * @param mixed $parameter 输入参数
     *
     * @return mixed
     * @throws Exception
     */
    public static function match(?string $pathInfo, $parameter = null)
    {
        foreach (self::$_routingTable as $key => $route) {
            if (preg_match($route['regx'], $pathInfo, $matches)) {
                self::$current = $key;

                try {
                    /** 载入参数 */
                    $params = null;

                    if (!empty($route['params'])) {
                        unset($matches[0]);
                        $params = array_combine($route['params'], $matches);
                    }

                    $widget = Typecho_Widget::widget($route['widget'], $parameter, $params);

                    return $widget;

                } catch (Exception $e) {
                    if (404 == $e->getCode()) {
                        Typecho_Widget::destroy($route['widget']);
                        continue;
                    }

                    throw $e;
                }
            }
        }

        return false;
    }

    /**
     * 路由分发函数
     *
     * @return void
     * @throws Exception
     */
    public static function dispatch()
    {
        /** 获取PATHINFO */
        $pathInfo = self::getPathInfo();

        foreach (self::$_routingTable as $key => $route) {
            if (preg_match($route['regx'], $pathInfo, $matches)) {
                self::$current = $key;

                try {
                    /** 载入参数 */
                    $params = null;

                    if (!empty($route['params'])) {
                        unset($matches[0]);
                        $params = array_combine($route['params'], $matches);
                    }

                    $widget = Typecho_Widget::widget($route['widget'], null, $params);

                    if (isset($route['action'])) {
                        $widget->{$route['action']}();
                    }

                    Typecho_Response::callback();
                    return;

                } catch (Exception $e) {
                    if (404 == $e->getCode()) {
                        Typecho_Widget::destroy($route['widget']);
                        continue;
                    }

                    throw $e;
                }
            }
        }

        /** 载入路由异常支持 */
        throw new Typecho_Router_Exception("Path '{$pathInfo}' not found", 404);
    }

    /**
     * 获取全路径
     *
     * @access public
     * @return string
     */
    public static function getPathInfo(): ?string
    {
        if (null === self::$_pathInfo) {
            self::setPathInfo();
        }

        return self::$_pathInfo;
    }

    /**
     * 设置全路径
     *
     * @access public
     *
     * @param string $pathInfo
     *
     * @return void
     */
    public static function setPathInfo(string $pathInfo = '/')
    {
        self::$_pathInfo = $pathInfo;
    }

    /**
     * 路由反解析函数
     *
     * @param string $name 路由配置表名称
     * @param array|null $value 路由填充值
     * @param string|null $prefix 最终合成路径的前缀
     *
     * @return string
     */
    public static function url(string $name, ?array $value = null, ?string $prefix = null): string
    {
        $route = self::$_routingTable[$name];

        //交换数组键值
        $pattern = [];
        foreach ($route['params'] as $row) {
            $pattern[$row] = $value[$row] ?? '{' . $row . '}';
        }

        return Typecho_Common::url(vsprintf($route['format'], $pattern), $prefix);
    }

    /**
     * 设置路由器默认配置
     *
     * @access public
     *
     * @param mixed $routes 配置信息
     *
     * @return void
     */
    public static function setRoutes($routes)
    {
        if (isset($routes[0])) {
            self::$_routingTable = $routes[0];
        } else {
            /** 解析路由配置 */
            $parser = new Typecho_Router_Parser($routes);
            self::$_routingTable = $parser->parse();
        }
    }

    /**
     * 获取路由信息
     *
     * @param string $routeName 路由名称
     *
     * @static
     * @access public
     * @return mixed
     */
    public static function get(string $routeName)
    {
        return self::$_routingTable[$routeName] ?? null;
    }
}
