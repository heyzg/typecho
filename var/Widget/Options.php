<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
/**
 * 全局选项
 *
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

/**
 * 全局选项组件
 *
 * @link typecho
 * @package Widget
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 */
class Widget_Options extends Typecho_Widget
{
    /**
     * 数据库对象
     *
     * @access protected
     * @var Typecho_Db
     */
    protected $db;

    /**
     * 缓存的插件配置
     *
     * @access private
     * @var array
     */
    private $_pluginConfig = [];

    /**
     * 缓存的个人插件配置
     *
     * @access private
     * @var array
     */
    private $_personalPluginConfig = [];

    /**
     * 构造函数,初始化组件
     *
     * @access public
     *
     * @param mixed $request request对象
     * @param mixed $response response对象
     * @param mixed $params 参数列表
     */
    public function __construct($request, $response, $params = null)
    {
        parent::__construct($request, $response);

        if (!empty($params)) {
            // 使用参数初始化而不使用数据库
            $this->row = $params;
        } else {
            /** 初始化数据库 */
            $this->db = Typecho_Db::get();
        }
    }

    /**
     * 执行函数
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        if (!empty($this->db)) {
            $values = $this->db->fetchAll($this->db->select()->from('table.options')
                ->where('user = 0'), [$this, 'push']);

            // finish install
            if (empty($values)) {
                $this->response->redirect(defined('__TYPECHO_ADMIN__')
                    ? '../install.php?step=3' : 'install.php?step=3');
            }
        }

        /** 支持皮肤变量重载 */
        if (!empty($this->row['theme:' . $this->row['theme']])) {
            $themeOptions = null;

            /** 解析变量 */
            if ($themeOptions = unserialize($this->row['theme:' . $this->row['theme']])) {
                /** 覆盖变量 */
                $this->row = array_merge($this->row, $themeOptions);
            }
        }

        $this->stack[] = &$this->row;

        /** 动态获取根目录 */
        $this->rootUrl = defined('__TYPECHO_ROOT_URL__') ? __TYPECHO_ROOT_URL__ : $this->request->getRequestRoot();
        if (defined('__TYPECHO_ADMIN__')) {
            /** 识别在admin目录中的情况 */
            $adminDir = '/' . trim(defined('__TYPECHO_ADMIN_DIR__') ? __TYPECHO_ADMIN_DIR__ : '/admin/', '/');
            $this->rootUrl = substr($this->rootUrl, 0, - strlen($adminDir));
        }

        /** 初始化站点信息 */
        if (defined('__TYPECHO_SITE_URL__')) {
            $this->siteUrl = __TYPECHO_SITE_URL__;
        } elseif (defined('__TYPECHO_DYNAMIC_SITE_URL__') && __TYPECHO_DYNAMIC_SITE_URL__) {
            $this->siteUrl = $this->rootUrl;
        }

        $this->originalSiteUrl = $this->siteUrl;
        $this->siteUrl = Typecho_Common::url(null, $this->siteUrl);
        $this->plugins = unserialize($this->plugins);

        /** 动态判断皮肤目录 */
        $this->theme = is_dir($this->themeFile($this->theme)) ? $this->theme : 'default';

        /** 增加对SSL连接的支持 */
        if ($this->request->isSecure() && 0 === strpos($this->siteUrl, 'http://')) {
            $this->siteUrl = substr_replace($this->siteUrl, 'https', 0, 4);
        }

        /** 自动初始化路由表 */
        $this->routingTable = unserialize($this->routingTable);
        if (!empty($this->db) && !isset($this->routingTable[0])) {
            /** 解析路由并缓存 */
            $parser = new Typecho_Router_Parser($this->routingTable);
            $parsedRoutingTable = $parser->parse();
            $this->routingTable = array_merge([$parsedRoutingTable], $this->routingTable);
            $this->db->query($this->db->update('table.options')->rows(['value' => serialize($this->routingTable)])
                ->where('name = ?', 'routingTable'));
        }
    }

    /**
     * 获取皮肤文件
     *
     * @param string $theme
     * @param string $file
     *
     * @return string
     */
    public function themeFile($theme, $file = '')
    {
        return __TYPECHO_ROOT_DIR__ . __TYPECHO_THEME_DIR__ . '/' . trim($theme, './') . '/' . trim($file, './');
    }

    /**
     * 重载父类push函数,将所有变量值压入堆栈
     *
     * @access public
     *
     * @param array $value 每行的值
     *
     * @return array
     */
    public function push(array $value)
    {
        //将行数据按顺序置位
        $this->row[$value['name']] = $value['value'];
        return $value;
    }

    /**
     * 输出网站路径
     *
     * @access public
     *
     * @param string $path 子路径
     *
     * @return void
     */
    public function siteUrl($path = null)
    {
        echo Typecho_Common::url($path, $this->siteUrl);
    }

    /**
     * 输出解析地址
     *
     * @access public
     *
     * @param string $path 子路径
     *
     * @return void
     */
    public function index($path = null)
    {
        echo Typecho_Common::url($path, $this->index);
    }

    /**
     * 输出模板路径
     *
     * @access public
     *
     * @param string $path 子路径
     * @param string $theme 模版名称
     *
     * @return string
     */
    public function themeUrl($path = null, $theme = null)
    {
        if (empty($theme)) {
            echo Typecho_Common::url($path, $this->themeUrl);
        }

        $url = defined('__TYPECHO_THEME_URL__') ? __TYPECHO_THEME_URL__ :
            Typecho_Common::url(__TYPECHO_THEME_DIR__ . '/' . $theme, $this->siteUrl);

        return Typecho_Common::url($path, $url);
    }

    /**
     * 输出插件路径
     *
     * @access public
     *
     * @param string $path 子路径
     *
     * @return void
     */
    public function pluginUrl($path = null)
    {
        echo Typecho_Common::url($path, $this->pluginUrl);
    }

    /**
     * 获取插件目录
     *
     * @param $plugin
     *
     * @return string
     */
    public function pluginDir($plugin = null)
    {
        return __TYPECHO_ROOT_DIR__ . '/' . __TYPECHO_PLUGIN_DIR__;
    }

    /**
     * 输出后台路径
     *
     * @access public
     *
     * @param string|null $path 子路径
     * @param bool $return
     *
     * @return void|string
     */
    public function adminUrl(?string $path = null, bool $return = false)
    {
        $url = Typecho_Common::url($path, $this->adminUrl);

        if ($return) {
            return $url;
        }

        echo $url;
    }

    /**
     * 获取或输出后台静态文件路径
     *
     * @param string $type
     * @param string|null $file
     * @param bool $return
     *
     * @return void|string
     */
    public function adminStaticUrl(string $type, ?string $file = null, bool $return = false)
    {
        $url = Typecho_Common::url($type, $this->adminUrl);

        if (empty($file)) {
            return $url;
        }

        $url = Typecho_Common::url($file, $url) . '?v=' . $this->version;

        if ($return) {
            return $url;
        }

        echo $url;
    }

    /**
     * 编码输出允许出现在评论中的html标签
     *
     * @access public
     * @return void
     */
    public function commentsHTMLTagAllowed()
    {
        echo htmlspecialchars($this->commentsHTMLTagAllowed);
    }

    /**
     * 获取插件系统参数
     *
     * @param mixed $pluginName 插件名称
     *
     * @return mixed
     * @throws Typecho_Plugin_Exception
     */
    public function plugin($pluginName)
    {
        if (!isset($this->_pluginConfig[$pluginName])) {
            if (!empty($this->row['plugin:' . $pluginName])
                && false !== ($options = unserialize($this->row['plugin:' . $pluginName]))) {
                $this->_pluginConfig[$pluginName] = new Typecho_Config($options);
            } else {
                throw new Typecho_Plugin_Exception(_t('插件%s的配置信息没有找到', $pluginName), 500);
            }
        }

        return $this->_pluginConfig[$pluginName];
    }

    /**
     * 获取个人插件系统参数
     *
     * @param mixed $pluginName 插件名称
     *
     * @return mixed
     * @throws Typecho_Plugin_Exception
     */
    public function personalPlugin($pluginName)
    {
        if (!isset($this->_personalPluginConfig[$pluginName])) {
            if (!empty($this->row['_plugin:' . $pluginName])
                && false !== ($options = unserialize($this->row['_plugin:' . $pluginName]))) {
                $this->_personalPluginConfig[$pluginName] = new Typecho_Config($options);
            } else {
                throw new Typecho_Plugin_Exception(_t('插件%s的配置信息没有找到', $pluginName), 500);
            }
        }

        return $this->_personalPluginConfig[$pluginName];
    }

    /**
     * RSS2.0
     *
     * @access protected
     * @return string
     */
    protected function ___feedUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/'], $this->index);
    }

    /**
     * RSS1.0
     *
     * @access protected
     * @return string
     */
    protected function ___feedRssUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/rss/'], $this->index);
    }

    /**
     * ATOM1.O
     *
     * @access protected
     * @return string
     */
    protected function ___feedAtomUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/atom/'], $this->index);
    }

    /**
     * 评论RSS2.0聚合
     *
     * @access protected
     * @return string
     */
    protected function ___commentsFeedUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/comments/'], $this->index);
    }

    /**
     * 评论RSS1.0聚合
     *
     * @access protected
     * @return string
     */
    protected function ___commentsFeedRssUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/rss/comments/'], $this->index);
    }

    /**
     * 评论ATOM1.0聚合
     *
     * @access protected
     * @return string
     */
    protected function ___commentsFeedAtomUrl()
    {
        return Typecho_Router::url('feed', ['feed' => '/atom/comments/'], $this->index);
    }

    /**
     * xmlrpc api地址
     *
     * @access protected
     * @return string
     */
    protected function ___xmlRpcUrl()
    {
        return Typecho_Router::url('do', ['action' => 'xmlrpc'], $this->index);
    }

    /**
     * 获取解析路径前缀
     *
     * @access protected
     * @return string
     */
    protected function ___index()
    {
        return ($this->rewrite || (defined('__TYPECHO_REWRITE__') && __TYPECHO_REWRITE__))
            ? $this->rootUrl : Typecho_Common::url('index.php', $this->rootUrl);
    }

    /**
     * 获取模板路径
     *
     * @access protected
     * @return string
     */
    protected function ___themeUrl()
    {
        return defined('__TYPECHO_THEME_URL__') ? __TYPECHO_THEME_URL__ :
            Typecho_Common::url(__TYPECHO_THEME_DIR__ . '/' . $this->theme, $this->siteUrl);
    }

    /**
     * 获取插件路径
     *
     * @access protected
     * @return string
     */
    protected function ___pluginUrl()
    {
        return defined('__TYPECHO_PLUGIN_URL__') ? __TYPECHO_PLUGIN_URL__ :
            Typecho_Common::url(__TYPECHO_PLUGIN_DIR__, $this->siteUrl);
    }

    /**
     * 获取后台路径
     *
     * @access protected
     * @return string
     */
    protected function ___adminUrl()
    {
        return Typecho_Common::url(defined('__TYPECHO_ADMIN_DIR__') ?
            __TYPECHO_ADMIN_DIR__ : '/admin/', $this->rootUrl);
    }

    /**
     * 获取登录地址
     *
     * @access protected
     * @return string
     */
    protected function ___loginUrl()
    {
        return Typecho_Common::url('login.php', $this->adminUrl);
    }

    /**
     * 获取登录提交地址
     *
     * @access protected
     * @return string
     */
    protected function ___loginAction()
    {
        return $this->widget('Widget_Security')->getTokenUrl(
            Typecho_Router::url('do', ['action' => 'login', 'widget' => 'Login'],
                Typecho_Common::url('index.php', $this->rootUrl)));
    }

    /**
     * 获取注册地址
     *
     * @access protected
     * @return string
     */
    protected function ___registerUrl()
    {
        return Typecho_Common::url('register.php', $this->adminUrl);
    }

    /**
     * 获取登录提交地址
     *
     * @access protected
     * @return string
     */
    protected function ___registerAction()
    {
        return $this->widget('Widget_Security')->getTokenUrl(
            Typecho_Router::url('do', ['action' => 'register', 'widget' => 'Register'], $this->index));
    }

    /**
     * 获取个人档案地址
     *
     * @access protected
     * @return string
     */
    protected function ___profileUrl()
    {
        return Typecho_Common::url('profile.php', $this->adminUrl);
    }

    /**
     * 获取登出地址
     *
     * @access protected
     * @return string
     */
    protected function ___logoutUrl()
    {
        return $this->widget('Widget_Security')->getTokenUrl(
            Typecho_Common::url('/action/logout', $this->index));
    }

    /**
     * 获取系统时区
     *
     * @access protected
     * @return integer
     */
    protected function ___serverTimezone()
    {
        return Typecho_Date::$serverTimezoneOffset;
    }

    /**
     * 获取GMT标准时间
     *
     * @return integer
     * @deprecated
     * @access protected
     */
    protected function ___gmtTime()
    {
        return Typecho_Date::gmtTime();
    }

    /**
     * 获取时间
     *
     * @return integer
     * @deprecated
     * @access protected
     */
    protected function ___time()
    {
        return Typecho_Date::time();
    }

    /**
     * 获取格式
     *
     * @access protected
     * @return string
     */
    protected function ___contentType()
    {
        return $this->contentType ?? 'text/html';
    }

    /**
     * 软件名称
     *
     * @access protected
     * @return string
     */
    protected function ___software()
    {
        [$software, $version] = explode(' ', $this->generator);
        return $software;
    }

    /**
     * 软件版本
     *
     * @access protected
     * @return string
     */
    protected function ___version()
    {
        [$software, $version] = explode(' ', $this->generator);
        $pos = strpos($version, '/');

        // fix for old version
        if ($pos !== false) {
            $version = substr($version, 0, $pos);
        }

        return $version;
    }

    /**
     * 允许上传的文件类型
     *
     * @access protected
     * @return string
     */
    protected function ___allowedAttachmentTypes()
    {
        $attachmentTypesResult = [];

        if (null != $this->attachmentTypes) {
            $attachmentTypes = str_replace(
                ['@image@', '@media@', '@doc@'],
                [
                    'gif,jpg,jpeg,png,tiff,bmp', 'mp3,mp4,mov,wmv,wma,rmvb,rm,avi,flv,ogg,oga,ogv',
                    'txt,doc,docx,xls,xlsx,ppt,pptx,zip,rar,pdf'
                ], $this->attachmentTypes);

            $attachmentTypesResult = array_unique(array_map('trim', preg_split("/(,|\.)/", $attachmentTypes)));
        }

        return $attachmentTypesResult;
    }
}
