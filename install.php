<?php if (!file_exists(dirname(__FILE__) . '/config.inc.php')): ?>
<?php
/**
 * Typecho Blog Platform
 *
 * @copyright  Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license    GNU General Public License 2.0
 * @version    $Id$
 */

// site root path
define('__TYPECHO_ROOT_DIR__', dirname(__FILE__));

// plugin directory (relative path)
define('__TYPECHO_PLUGIN_DIR__', '/usr/plugins');

// theme directory (relative path)
define('__TYPECHO_THEME_DIR__', '/usr/themes');

// admin directory (relative path)
define('__TYPECHO_ADMIN_DIR__', '/admin/');

// register autoload
require_once __TYPECHO_ROOT_DIR__ . '/var/Typecho/Common.php';

// init
Typecho_Common::init();

else:

    require_once dirname(__FILE__) . '/config.inc.php';
    $installDb = Typecho_Db::get();

endif;

/**
 * get lang
 *
 * @return string
 */
function install_get_lang(): string {
    $serverLang = Typecho_Request::getInstance()->getServer('TYPECHO_LANG');

    if (!empty($serverLang)) {
        return $serverLang;
    } else {
        $lang = 'zh_CN';
        $request = Typecho_Request::getInstance();

        if ($request->is('lang')) {
            $lang = $request->get('lang');
            Typecho_Cookie::set('lang', $lang);
        }

        return Typecho_Cookie::get('lang', $lang);
    }
}

/**
 * get site url
 *
 * @return string
 */
function install_get_site_url(): string {
    $serverSiteUrl = Typecho_Request::getInstance()->getServer(
        'TYPECHO_SITE_URL',
        install_is_cli() ? 'http://localhost' : null
    );

    if (!empty($serverSiteUrl)) {
        return $serverSiteUrl;
    } else {
        $request = Typecho_Request::getInstance();
        return $request->get('userUrl', $request->getRequestRoot());
    }
}

/**
 * detect cli mode
 *
 * @return bool
 */
function install_is_cli(): bool {
    return php_sapi_name() == 'cli';
}

/**
 * list all default options
 *
 * @return array
 */
function install_get_default_options(): array {
    static $options;

    if (empty($options)) {
        $options = [
            'theme' => 'default',
            'theme:default' => 'a:2:{s:7:"logoUrl";N;s:12:"sidebarBlock";a:5:{i:0;s:15:"ShowRecentPosts";i:1;s:18:"ShowRecentComments";i:2;s:12:"ShowCategory";i:3;s:11:"ShowArchive";i:4;s:9:"ShowOther";}}',
            'timezone' => '28800',
            'lang' => install_get_lang(),
            'charset' => _t('UTF-8'),
            'contentType' => 'text/html',
            'gzip' => 0,
            'generator' => 'Typecho ' . Typecho_Common::VERSION,
            'title' => 'Hello World',
            'description' => 'Your description here.',
            'keywords' => 'typecho,php,blog',
            'rewrite' => 0,
            'frontPage' => 'recent',
            'frontArchive' => 0,
            'commentsRequireMail' => 1,
            'commentsWhitelist' => 0,
            'commentsRequireURL' => 0,
            'commentsRequireModeration' => 0,
            'plugins' => 'a:0:{}',
            'commentDateFormat' => 'F jS, Y \a\t h:i a',
            'siteUrl' => install_get_site_url(),
            'defaultCategory' => 1,
            'allowRegister' => 0,
            'defaultAllowComment' => 1,
            'defaultAllowPing' => 1,
            'defaultAllowFeed' => 1,
            'pageSize' => 5,
            'postsListSize' => 10,
            'commentsListSize' => 10,
            'commentsHTMLTagAllowed' => null,
            'postDateFormat' => 'Y-m-d',
            'feedFullText' => 1,
            'editorSize' => 350,
            'autoSave' => 0,
            'markdown' => 1,
            'xmlrpcMarkdown' => 0,
            'commentsMaxNestingLevels' => 5,
            'commentsPostTimeout' => 24 * 3600 * 30,
            'commentsUrlNofollow' => 1,
            'commentsShowUrl' => 1,
            'commentsMarkdown' => 0,
            'commentsPageBreak' => 0,
            'commentsThreaded' => 1,
            'commentsPageSize' => 20,
            'commentsPageDisplay' => 'last',
            'commentsOrder' => 'ASC',
            'commentsCheckReferer' => 1,
            'commentsAutoClose' => 0,
            'commentsPostIntervalEnable' => 1,
            'commentsPostInterval' => 60,
            'commentsShowCommentOnly' => 0,
            'commentsAvatar' => 1,
            'commentsAvatarRating' => 'G',
            'commentsAntiSpam' => 1,
            'routingTable' => 'a:25:{s:5:"index";a:3:{s:3:"url";s:1:"/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:7:"archive";a:3:{s:3:"url";s:6:"/blog/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:2:"do";a:3:{s:3:"url";s:22:"/action/[action:alpha]";s:6:"widget";s:9:"Widget_Do";s:6:"action";s:6:"action";}s:4:"post";a:3:{s:3:"url";s:24:"/archives/[cid:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:10:"attachment";a:3:{s:3:"url";s:26:"/attachment/[cid:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:8:"category";a:3:{s:3:"url";s:17:"/category/[slug]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:3:"tag";a:3:{s:3:"url";s:12:"/tag/[slug]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:6:"author";a:3:{s:3:"url";s:22:"/author/[uid:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:6:"search";a:3:{s:3:"url";s:19:"/search/[keywords]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:10:"index_page";a:3:{s:3:"url";s:21:"/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:12:"archive_page";a:3:{s:3:"url";s:26:"/blog/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:13:"category_page";a:3:{s:3:"url";s:32:"/category/[slug]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:8:"tag_page";a:3:{s:3:"url";s:27:"/tag/[slug]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:11:"author_page";a:3:{s:3:"url";s:37:"/author/[uid:digital]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:11:"search_page";a:3:{s:3:"url";s:34:"/search/[keywords]/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:12:"archive_year";a:3:{s:3:"url";s:18:"/[year:digital:4]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:13:"archive_month";a:3:{s:3:"url";s:36:"/[year:digital:4]/[month:digital:2]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:11:"archive_day";a:3:{s:3:"url";s:52:"/[year:digital:4]/[month:digital:2]/[day:digital:2]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:17:"archive_year_page";a:3:{s:3:"url";s:38:"/[year:digital:4]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:18:"archive_month_page";a:3:{s:3:"url";s:56:"/[year:digital:4]/[month:digital:2]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:16:"archive_day_page";a:3:{s:3:"url";s:72:"/[year:digital:4]/[month:digital:2]/[day:digital:2]/page/[page:digital]/";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:12:"comment_page";a:3:{s:3:"url";s:53:"[permalink:string]/comment-page-[commentPage:digital]";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}s:4:"feed";a:3:{s:3:"url";s:20:"/feed[feed:string:0]";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:4:"feed";}s:8:"feedback";a:3:{s:3:"url";s:31:"[permalink:string]/[type:alpha]";s:6:"widget";s:15:"Widget_Feedback";s:6:"action";s:6:"action";}s:4:"page";a:3:{s:3:"url";s:12:"/[slug].html";s:6:"widget";s:14:"Widget_Archive";s:6:"action";s:6:"render";}}',
            'actionTable' => 'a:0:{}',
            'panelTable' => 'a:0:{}',
            'attachmentTypes' => '@image@',
            'secret' => Typecho_Common::randString(32, true),
            'installed' => 0,
            'allowXmlRpc' => 2
        ];
    }

    return $options;
}

/**
 * get database driver type
 *
 * @param string $driver
 * @return string
 */
function install_get_db_type(string $driver): string {
    $parts = explode('_', $driver);
    return $driver == 'Mysqli' ? 'Mysql' : array_pop($parts);
}

/**
 * list all available database drivers
 *
 * @return array
 */
function install_get_db_drivers(): array {
    $drivers = [];

    if (Typecho_Db_Adapter_Pdo_Mysql::isAvailable()) {
        $drivers['Pdo_Mysql'] = _t('Pdo 驱动 Mysql 适配器');
    }

    if (Typecho_Db_Adapter_Pdo_SQLite::isAvailable()) {
        $drivers['Pdo_SQLite'] = _t('Pdo 驱动 SQLite 适配器 (SQLite 3.x)');
    }

    if (Typecho_Db_Adapter_Pdo_Pgsql::isAvailable()) {
        $drivers['Pdo_Pgsql'] = _t('Pdo 驱动 PostgreSql 适配器');
    }

    if (Typecho_Db_Adapter_Mysqli::isAvailable()) {
        $drivers['Mysqli'] = _t('Mysql 原生函数适配器');
    }

    if (Typecho_Db_Adapter_SQLite::isAvailable()) {
        $drivers['SQLite'] = _t('SQLite 原生函数适配器 (SQLite 2.x)');
    }

    if (Typecho_Db_Adapter_Pgsql::isAvailable()) {
        $drivers['Pgsql'] = _t('Pgsql 原生函数适配器');
    }

    return $drivers;
}

/**
 * get current db driver
 *
 * @return string
 */
function install_get_current_db_driver(): string {
    global $installDb;

    if (empty($installDb)) {
        $driver = Typecho_Request::getInstance()->get('driver');
        $drivers = install_get_db_drivers();

        if (empty($driver) || !isset($drivers[$driver])) {
            return key($drivers);
        }

        return $driver;
    } else {
        return $installDb->getAdapterName();
    }
}

/**
 * generate config file
 *
 * @param string $adapter
 * @param string $dbPrefix
 * @param array $dbConfig
 * @param bool $return
 * @return string
 */
function install_config_file(string $adapter, string $dbPrefix, array $dbConfig, bool $return = false): string {
    global $configWritten;

    $lines = array_slice(file(__FILE__), 1, 26);
    $lines[] = "
/** 定义数据库参数 */
\$db = new Typecho_Db('{$adapter}', '{$dbPrefix}');
\$db->addServer(" . (var_export($dbConfig, true)) . ", Typecho_Db::READ | Typecho_Db::WRITE);
Typecho_Db::set(\$db);
";

    $code = implode('', $lines);
    $configWritten = false;

    if (!$return) {
        $configWritten = @file_put_contents(__TYPECHO_ROOT_DIR__ . '/config.inc.php', $code) !== false;
    }

    return $code;
}

/**
 * remove config file if written
 */
function install_remove_config_file() {
    global $configWritten;

    if ($configWritten) {
        unlink(__TYPECHO_ROOT_DIR__ . '/config.inc.php');
    }
}

/**
 * check install
 *
 * @param string $type
 * @return bool
 */
function install_check(string $type): bool {
    switch ($type) {
        case 'config':
            return file_exists(__TYPECHO_ROOT_DIR__ . '/config.inc.php');
        case 'db_structure':
        case 'db_data':
            global $installDb;

            if (empty($installDb)) {
                return false;
            }

            try {
                // check if table exists
                $values = $installDb->fetchAll($installDb->select()->from('table.options')
                    ->where('user = 0'));

                if ($type == 'db_data' && empty($values)) {
                    return false;
                }
            } catch (Typecho_Db_Exception $e) {
                return false;
            }

            return true;
        default:
            return false;
    }
}

/**
 * raise install error
 *
 * @param mixed $error
 * @param mixed $config
 */
function install_raise_error($error, $config = null) {
    if (install_is_cli()) {
        if (is_array($error)) {
            foreach ($error as $key => $value) {
                echo (is_int($key) ? '' : $key . ': ') . $value . "\n";
            }
        } else {
            echo $error . "\n";
        }

        exit(1);
    } else {
        Typecho_Response::getInstance()->throwJson([
            'success' => 0,
            'message' => is_string($error) ? nl2br($error) : $error,
            'config' => $config
        ]);
    }
}

/**
 * @param $step
 * @param array|null $config
 */
function install_success($step, ?array $config = null) {
    if (install_is_cli()) {
        if ($step > 0) {
            $method = 'install_step_' . $step . '_perform';
            $method();
        }

        if (!empty($config)) {
            [$userName, $userPassword] = $config;
            echo _t('安装成功') . "\n";
            echo _t('您的用户名是') . " {$userName}\n";
            echo _t('您的密码是') . " {$userPassword}\n";
        }

        exit(0);
    } else {
        Typecho_Response::getInstance()->throwJson([
            'success' => 1,
            'message' => $step,
            'config'  => $config
        ]);
    }
}

/**
 * add common js support
 *
 * @throws Typecho_Exception
 */
function install_js_support() {
    $options = Typecho_Widget::widget('Widget_Options');

    ?>
    <div id="success" class="row typecho-page-main hidden">
        <div class="col-mb-12 col-tb-8 col-tb-offset-2">
            <div class="typecho-page-title">
                <h2><?php _e('安装成功'); ?></h2>
            </div>
            <div id="typecho-welcome">
                <p class="keep-word">
                    <?php _e('您选择了使用原有的数据, 您的用户名和密码和原来的一致'); ?>
                </p>
                <p class="fresh-word">
                    <?php _e('您的用户名是'); ?>: <strong class="warning" id="success-user"></strong><br>
                    <?php _e('您的密码是'); ?>: <strong class="warning" id="success-password"></strong>
                </p>
                <ul>
                    <li><a id="login-url" href=""><?php _e('点击这里访问您的控制面板'); ?></a></li>
                    <li><a id="site-url" href=""><?php _e('点击这里查看您的 Blog'); ?></a></li>
                </ul>
                <p><?php _e('希望您能尽情享用 Typecho 带来的乐趣!'); ?></p>
            </div>
        </div>
    </div>
    <script>
        let form = $('form'), errorBox = $('<div></div>');

        errorBox.addClass('message error')
            .prependTo(form);

        function showError(error) {
            if (typeof error == 'string') {
                $(window).scrollTop(0);

                errorBox
                    .html(error)
                    .addClass('fade');
            } else {
                for (let k in error) {
                    let input = $('#' + k), msg = error[k], p = $('<p></p>');

                    p.addClass('message error')
                        .html(msg)
                        .insertAfter(input);

                    input.on('input', function () {
                        p.remove();
                    });
                }
            }

            return errorBox;
        }

        form.submit(function (e) {
            e.preventDefault();

            errorBox.removeClass('fade');
            $('button', form).attr('disabled', 'disabled');
            $('.typecho-option .error', form).remove();

            $.ajax({
                url: form.attr('action'),
                processData: false,
                contentType: false,
                type: 'POST',
                data: new FormData(this),
                success: function (data) {
                    $('button', form).removeAttr('disabled');

                    if (data.success) {
                        if (data.message) {
                            location.href = '?step=' + data.message;
                        } else {
                            let success = $('#success').removeClass('hidden');

                            form.addClass('hidden');

                            if (data.config) {
                                success.addClass('fresh');

                                $('.typecho-page-main:first').addClass('hidden');
                                $('#success-user').html(data.config[0]);
                                $('#success-password').html(data.config[1]);

                                $('#login-url').attr('href', data.config[2]);
                                $('#site-url').attr('href', data.config[3]);
                            } else {
                                success.addClass('keep');
                            }
                        }
                    } else {
                        let el = showError(data.message);

                        if (typeof configError == 'function' && data.config) {
                            configError(form, data.config, el);
                        }
                    }
                },
                error: function (xhr, error) {
                    showError(error)
                }
            });
        });
    </script>
<?php
}

function install_step_1() {
    $langs = Widget_Options_General::getLangs();
    $lang = install_get_lang();
?>
    <div class="row typecho-page-main">
        <div class="col-mb-12 col-tb-8 col-tb-offset-2">
            <div class="typecho-page-title">
                <h2><?php _e('欢迎使用 Typecho'); ?></h2>
            </div>
            <div id="typecho-welcome">
                <form autocomplete="off" method="get" action="install.php">
                    <h3><?php _e('安装说明'); ?></h3>
                    <p class="warning">
                        <strong><?php _e('本安装程序将自动检测服务器环境是否符合最低配置需求. 如果不符合, 将在上方出现提示信息, 请按照提示信息检查您的主机配置. 如果服务器环境符合要求, 将在下方出现 "开始下一步" 的按钮, 点击此按钮即可一步完成安装.'); ?></strong>
                    </p>
                    <h3><?php _e('许可及协议'); ?></h3>
                    <ul>
                        <li><?php _e('Typecho 基于 <a href="http://www.gnu.org/copyleft/gpl.html">GPL</a> 协议发布, 我们允许用户在 GPL 协议许可的范围内使用, 拷贝, 修改和分发此程序.'); ?>
                            <?php _e('在GPL许可的范围内, 您可以自由地将其用于商业以及非商业用途.'); ?></li>
                        <li><?php _e('Typecho 软件由其社区提供支持, 核心开发团队负责维护程序日常开发工作以及新特性的制定.'); ?>
                            <?php _e('如果您遇到使用上的问题, 程序中的 BUG, 以及期许的新功能, 欢迎您在社区中交流或者直接向我们贡献代码.'); ?>
                            <?php _e('对于贡献突出者, 他的名字将出现在贡献者名单中.'); ?></li>
                    </ul>

                    <p class="submit">
                        <button class="btn primary" type="submit"><?php _e('我准备好了, 开始下一步 &raquo;'); ?></button>
                        <input type="hidden" name="step" value="2">

                        <?php if (count($langs) > 1): ?>
                            <select style="float: right" onchange="location.href='?lang=' + this.value">
                                <?php foreach ($langs as $key => $val): ?>
                                    <option value="<?php echo $key; ?>"<?php if ($lang == $key): ?> selected<?php endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </p>
                </form>
            </div>
        </div>
    </div>
<?php
}

/**
 * display step 2
 */
function install_step_2() {
    global $installDb;

    $drivers = install_get_db_drivers();
    $adapter = install_get_current_db_driver();
    $type = install_get_db_type($adapter);

    if (!empty($installDb)) {
        $config = $installDb->getConfig(Typecho_Db::WRITE)->toArray();
        $config['prefix'] = $installDb->getPrefix();
        $config['adapter'] = $adapter;
    }
?>
    <div class="row typecho-page-main">
        <div class="col-mb-12 col-tb-8 col-tb-offset-2">
            <div class="typecho-page-title">
                <h2><?php _e('初始化配置'); ?></h2>
            </div>
            <form autocomplete="off" action="install.php" method="post">
                <ul class="typecho-option">
                    <li>
                        <label for="dbAdapter" class="typecho-label"><?php _e('数据库适配器'); ?></label>
                        <select name="dbAdapter" id="dbAdapter" onchange="location.href='?step=2&driver=' + this.value">
                            <?php foreach ($drivers as $driver => $name): ?>
                                <option value="<?php echo $driver; ?>"<?php if($driver == $adapter): ?> selected="selected"<?php endif; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php _e('请根据您的数据库类型选择合适的适配器'); ?></p>
                        <input type="hidden" id="dbNext" name="dbNext" value="none">
                    </li>
                </ul>
                <ul class="typecho-option">
                    <li>
                        <label class="typecho-label" for="dbPrefix"><?php _e('数据库前缀'); ?></label>
                        <input type="text" class="text" name="dbPrefix" id="dbPrefix" value="typecho_" />
                        <p class="description"><?php _e('默认前缀是 "typecho_"'); ?></p>
                    </li>
                </ul>
                <?php require_once './install/' . $type . '.php'; ?>


                <ul class="typecho-option typecho-option-submit">
                    <li>
                        <button id="confirm" type="submit" class="btn primary"><?php _e('确认, 开始安装 &raquo;'); ?></button>
                        <input type="hidden" name="step" value="2">
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <script>
        function configError(form, config, errorBox) {
            let next = $('#dbNext'),
                line = $('<p></p>');

            if (config.code) {
                let text = $('<textarea></textarea>'),
                    btn = $('<button></button>');

                btn.html('<?php _e('创建完毕, 继续安装 &raquo;'); ?>')
                    .attr('type', 'button')
                    .addClass('btn btn-s primary');

                btn.click(function () {
                    next.val('config');
                    form.trigger('submit');
                });

                text.val(config.code)
                    .addClass('mono')
                    .attr('readonly', 'readonly');

                errorBox.append(text)
                    .append(btn);
                return;
            }

            errorBox.append(line);

            for (let key in config) {
                let word = config[key],
                    btn = $('<button></button>');

                btn.html(word)
                    .attr('type', 'button')
                    .addClass('btn btn-s primary')
                    .click(function () {
                        next.val(key);
                        form.trigger('submit');
                    });

                line.append(btn);
            }
        }

        $('#confirm').click(function () {
            $('#dbNext').val('none');
        });

        <?php if (!empty($config)): ?>
        function fillInput(config) {
            for (let k in config) {
                let value = config[k],
                    key = 'db' + k.charAt(0).toUpperCase() + k.slice(1),
                    input = $('#' + key)
                        .attr('readonly', 'readonly')
                        .val(value);

                $('option:not(:selected)', input).attr('disabled', 'disabled');
            }
        }

        fillInput(<?php echo Json::encode($config); ?>);
        <?php endif; ?>
    </script>
<?php
    install_js_support();
}

/**
 * perform install step 2
 */
function install_step_2_perform() {
    global $installDb;

    $request = Typecho_Request::getInstance();
    $drivers = install_get_db_drivers();

    $configMap = [
        'Mysql' => [
            'dbHost' => 'localhost',
            'dbPort' => 3306,
            'dbUser' => null,
            'dbPassword' => null,
            'dbCharset' => 'utf8mb4',
            'dbDatabase' => null,
            'dbEngine' => 'InnoDB'
        ],
        'Pgsql' => [
            'dbHost' => 'localhost',
            'dbPort' => 5432,
            'dbUser' => null,
            'dbPassword' => null,
            'dbCharset' => 'utf8',
            'dbDatabase' => null,
        ],
        'SQLite' => [
            'dbFile' => __TYPECHO_ROOT_DIR__ . '/usr/' . uniqid() . '.db'
        ]
    ];

    if (install_is_cli()) {
        $config = [
            'dbHost' => $request->getServer('TYPECHO_DB_HOST'),
            'dbUser' => $request->getServer('TYPECHO_DB_USER'),
            'dbPassword' => $request->getServer('TYPECHO_DB_PASSWORD'),
            'dbCharset' => $request->getServer('TYPECHO_DB_CHARSET'),
            'dbPort' => $request->getServer('TYPECHO_DB_PORT'),
            'dbDatabase' => $request->getServer('TYPECHO_DB_DATABASE'),
            'dbFile' => $request->getServer('TYPECHO_DB_FILE'),
            'dbDsn' => $request->getServer('TYPECHO_DB_DSN'),
            'dbEngine' => $request->getServer('TYPECHO_DB_ENGINE'),
            'dbPrefix' => $request->getServer('TYPECHO_DB_PREFIX', 'typecho_'),
            'dbAdapter' => $request->getServer('TYPECHO_DB_ADAPTER', install_get_current_db_driver()),
            'dbNext' => $request->getServer('TYPECHO_DB_NEXT', 'none')
        ];
    } else {
        $config = $request->from([
            'dbHost',
            'dbUser',
            'dbPassword',
            'dbCharset',
            'dbPort',
            'dbDatabase',
            'dbFile',
            'dbDsn',
            'dbEngine',
            'dbPrefix',
            'dbAdapter',
            'dbNext'
        ]);
    }

    $error = (new Typecho_Validate())
        ->addRule('dbPrefix', 'required', _t('确认您的配置'))
        ->addRule('dbPrefix', 'minLength', _t('确认您的配置'), 1)
        ->addRule('dbPrefix', 'maxLength', _t('确认您的配置'), 16)
        ->addRule('dbPrefix', 'alphaDash', _t('确认您的配置'))
        ->addRule('dbAdapter', 'required', _t('确认您的配置'))
        ->addRule('dbAdapter', 'enum', _t('确认您的配置'), array_keys($drivers))
        ->addRule('dbNext', 'required', _t('确认您的配置'))
        ->addRule('dbNext', 'enum', _t('确认您的配置'), ['none', 'delete', 'keep', 'config'])
        ->run($config);

    if (!empty($error)) {
        install_raise_error($error);
    }

    $type = install_get_db_type($config['dbAdapter']);
    $dbConfig = [];

    foreach ($configMap[$type] as $key => $value) {
        $config[$key] = $config[$key] === null ? (install_is_cli() ? $value : null) : $config[$key];
    }

    switch ($type) {
        case 'Mysql':
            $error = (new Typecho_Validate())
                ->addRule('dbHost', 'required', _t('确认您的配置'))
                ->addRule('dbPort', 'required', _t('确认您的配置'))
                ->addRule('dbPort', 'isInteger', _t('确认您的配置'))
                ->addRule('dbUser', 'required', _t('确认您的配置'))
                ->addRule('dbCharset', 'required', _t('确认您的配置'))
                ->addRule('dbCharset', 'enum', _t('确认您的配置'), ['utf8', 'utf8mb4'])
                ->addRule('dbDatabase', 'required', _t('确认您的配置'))
                ->addRule('dbEngine', 'required', _t('确认您的配置'))
                ->addRule('dbEngine', 'enum', _t('确认您的配置'), ['InnoDB', 'MyISAM'])
                ->run($config);
            break;
        case 'Pgsql':
            $error = (new Typecho_Validate())
                ->addRule('dbHost', 'required', _t('确认您的配置'))
                ->addRule('dbPort', 'required', _t('确认您的配置'))
                ->addRule('dbPort', 'isInteger', _t('确认您的配置'))
                ->addRule('dbUser', 'required', _t('确认您的配置'))
                ->addRule('dbCharset', 'required', _t('确认您的配置'))
                ->addRule('dbCharset', 'enum', _t('确认您的配置'), ['utf8'])
                ->addRule('dbDatabase', 'required', _t('确认您的配置'))
                ->run($config);
            break;
        case 'SQLite':
            $error = (new Typecho_Validate())
                ->addRule('dbFile', 'required', _t('确认您的配置'))
                ->run($config);
            break;
        default:
            install_raise_error(_t('确认您的配置'));
            break;
    }

    if (!empty($error)) {
        install_raise_error($error);
    }

    foreach ($configMap[$type] as $key => $value) {
        $dbConfig[strtolower(substr($key, 2))] = $config[$key];
    }

    // check config file
    if ($config['dbNext'] == 'config' && !install_check('config')) {
        $code = install_config_file($config['dbAdapter'], $config['dbPrefix'], $dbConfig, true);
        install_raise_error(_t('没有检测到您手动创建的配置文件, 请检查后再次创建'), ['code' => $code]);
    } elseif (empty($installDb)) {
        // detect db config
        try {
            $installDb = new Typecho_Db($config['dbAdapter'], $config['dbPrefix']);
            $installDb->addServer($dbConfig, Typecho_Db::READ | Typecho_Db::WRITE);
            $installDb->query('SELECT 1=1');
        } catch (Typecho_Db_Adapter_Exception $e) {
            install_raise_error(_t('对不起, 无法连接数据库, 请先检查数据库配置再继续进行安装'));
        } catch (Typecho_Db_Exception $e) {
            install_raise_error(_t('安装程序捕捉到以下错误: " %s ". 程序被终止, 请检查您的配置信息.', $e->getMessage()));
        }

        $code = install_config_file($config['dbAdapter'], $config['dbPrefix'], $dbConfig);

        if (!install_check('config')) {
            install_raise_error(
                _t('安装程序无法自动创建 <strong>config.inc.php</strong> 文件') . "\n" .
                _t('您可以在网站根目录下手动创建 <strong>config.inc.php</strong> 文件, 并复制如下代码至其中')
            , [
                'code' => $code
            ]);
        }
    }

    // delete exists db
    if($config['dbNext'] == 'delete') {
        $tables = [
            $config['dbPrefix'] . 'comments',
            $config['dbPrefix'] . 'contents',
            $config['dbPrefix'] . 'fields',
            $config['dbPrefix'] . 'metas',
            $config['dbPrefix'] . 'options',
            $config['dbPrefix'] . 'relationships',
            $config['dbPrefix'] . 'users'
        ];

        try {
            foreach ($tables as $table) {
                if ($type == 'Mysql') {
                    $installDb->query("DROP TABLE IF EXISTS `{$table}`");
                } elseif ($type == 'Pgsql') {
                    $installDb->query("DROP TABLE {$table}");
                } elseif ($type == 'SQLite') {
                    $installDb->query("DROP TABLE {$table}");
                }
            }
        } catch (Typecho_Db_Exception $e) {
            install_raise_error(_t('安装程序捕捉到以下错误: "%s". 程序被终止, 请检查您的配置信息.', $e->getMessage()));
        }
    }

    // init db structure
    try {
        $scripts = file_get_contents(__TYPECHO_ROOT_DIR__ . '/install/' . $type . '.sql');
        $scripts = str_replace('typecho_', $config['dbPrefix'], $scripts);

        if (isset($dbConfig['charset'])) {
            $scripts = str_replace('%charset%', $dbConfig['charset'], $scripts);
        }

        if (isset($dbConfig['engine'])) {
            $scripts = str_replace('%engine%', $dbConfig['engine'], $scripts);
        }

        $scripts = explode(';', $scripts);
        foreach ($scripts as $script) {
            $script = trim($script);
            if ($script) {
                $installDb->query($script, Typecho_Db::WRITE);
            }
        }
    } catch (Typecho_Db_Exception $e) {
        $code = $e->getCode();

        if(('Mysql' == $type && (1050 == $code || '42S01' == $code)) ||
            ('SQLite' == $type && ('HY000' == $code || 1 == $code)) ||
            ('Pgsql' == $type && '42P07' == $code)) {

            if ($config['dbNext'] == 'keep') {
                if (install_check('db_data')) {
                    install_success(0);
                } else {
                    install_success(3);
                }
            } elseif ($config['dbNext'] == 'none') {
                install_remove_config_file();

                install_raise_error(_t('安装程序检查到原有数据表已经存在.'), [
                    'delete' => _t('删除原有数据'),
                    'keep' => _t('使用原有数据')
                ]);
            }
        } else {
            install_remove_config_file();

            install_raise_error(_t('安装程序捕捉到以下错误: "%s". 程序被终止, 请检查您的配置信息.', $e->getMessage()));
        }
    }

    install_success(3);
}

/**
 * display step 3
 */
function install_step_3() {
    $options = Typecho_Widget::widget('Widget_Options');
?>
    <div class="row typecho-page-main">
        <div class="col-mb-12 col-tb-8 col-tb-offset-2">
            <div class="typecho-page-title">
                <h2><?php _e('创建您的管理员帐号'); ?></h2>
            </div>
            <form autocomplete="off" action="install.php" method="post">
                <ul class="typecho-option">
                    <li>
                        <label class="typecho-label" for="userUrl"><?php _e('网站地址'); ?></label>
                        <input autocomplete="new-password" type="text" name="userUrl" id="userUrl" class="text" value="<?php $options->siteUrl(); ?>" />
                        <p class="description"><?php _e('这是程序自动匹配的网站路径, 如果不正确请修改它'); ?></p>
                    </li>
                </ul>
                <ul class="typecho-option">
                    <li>
                        <label class="typecho-label" for="userName"><?php _e('用户名'); ?></label>
                        <input autocomplete="new-password" type="text" name="userName" id="userName" class="text" />
                        <p class="description"><?php _e('请填写您的用户名'); ?></p>
                    </li>
                </ul>
                <ul class="typecho-option">
                    <li>
                        <label class="typecho-label" for="userPassword"><?php _e('登录密码'); ?></label>
                        <input type="password" name="userPassword" id="userPassword" class="text" />
                        <p class="description"><?php _e('请填写您的登录密码, 如果留空系统将为您随机生成一个'); ?></p>
                    </li>
                </ul>
                <ul class="typecho-option">
                    <li>
                        <label class="typecho-label" for="userMail"><?php _e('邮件地址'); ?></label>
                        <input autocomplete="new-password" type="text" name="userMail" id="userMail" class="text" />
                        <p class="description"><?php _e('请填写一个您的常用邮箱'); ?></p>
                    </li>
                </ul>
                <ul class="typecho-option typecho-option-submit">
                    <li>
                        <button type="submit" class="btn primary"><?php _e('继续安装 &raquo;'); ?></button>
                        <input type="hidden" name="step" value="3">
                    </li>
                </ul>
            </form>
        </div>
    </div>
<?php
    install_js_support();
}

/**
 * perform step 3
 */
function install_step_3_perform() {
    global $installDb;

    $request = Typecho_Request::getInstance();
    $defaultPassword = Typecho_Common::randString(8);
    $options = Typecho_Widget::widget('Widget_Options');

    if (install_is_cli()) {
        $config = [
            'userUrl' => $request->getServer('TYPECHO_SITE_URL'),
            'userName' => $request->getServer('TYPECHO_USER_NAME', 'typecho'),
            'userPassword' => $request->getServer('TYPECHO_USER_PASSWORD'),
            'userMail' => $request->getServer('TYPECHO_USER_MAIL', 'admin@localhost.local')
        ];
    } else {
        $config = $request->from([
            'userUrl',
            'userName',
            'userPassword',
            'userMail',
        ]);
    }

    $error = (new Typecho_Validate())
        ->addRule('userUrl', 'required', _t('请填写站点地址'))
        ->addRule('userUrl', 'url', _t('请填写一个合法的URL地址'))
        ->addRule('userName', 'required',  _t('必须填写用户名称'))
        ->addRule('userName', 'xssCheck', _t('请不要在用户名中使用特殊字符'))
        ->addRule('userName', 'maxLength', _t('用户名长度超过限制, 请不要超过 32 个字符'), 32)
        ->addRule('userMail', 'required', _t('必须填写电子邮箱'))
        ->addRule('userMail', 'email', _t('电子邮箱格式错误'))
        ->addRule('userMail', 'maxLength', _t('邮箱长度超过限制, 请不要超过 200 个字符'), 200)
        ->run($config);

    if (!empty($error)) {
        install_raise_error($error);
    }

    if (empty($config['userPassword'])) {
        $config['userPassword'] = $defaultPassword;
    }

    try {
        // write options
        foreach (install_get_default_options() as $key => $value) {
            $installDb->query(
                $installDb->insert('table.options')->rows(['name' => $key, 'user' => 0, 'value' => $value])
            );
        }

        // write user
        $hasher = new PasswordHash(8, true);
        $installDb->query(
            $installDb->insert('table.users')->rows([
                'name' => $config['userName'],
                'password' => $hasher->HashPassword($config['userPassword']),
                'mail' => $config['userMail'],
                'url' => $options->siteUrl,
                'screenName' => $config['userName'],
                'group' => 'administrator',
                'created' => Typecho_Date::time()
            ])
        );

        // write category
        $installDb->query(
            $installDb->insert('table.metas')
                ->rows([
                    'name' => _t('默认分类'),
                    'slug' => 'default',
                    'type' => 'category',
                    'description' => _t('只是一个默认分类'),
                    'count' => 1
                ])
        );

        $installDb->query($installDb->insert('table.relationships')->rows(['cid' => 1, 'mid' => 1]));

        // write first page and post
        $installDb->query(
            $installDb->insert('table.contents')->rows([
                'title' => _t('欢迎使用 Typecho'),
                'slug' => 'start', 'created' => Typecho_Date::time(),
                'modified' => Typecho_Date::time(),
                'text' => '<!--markdown-->' . _t('如果您看到这篇文章,表示您的 blog 已经安装成功.'),
                'authorId' => 1,
                'type' => 'post',
                'status' => 'publish',
                'commentsNum' => 1,
                'allowComment' => 1,
                'allowPing' => 1,
                'allowFeed' => 1,
                'parent' => 0
            ])
        );

        $installDb->query(
            $installDb->insert('table.contents')->rows([
                'title' => _t('关于'),
                'slug' => 'start-page',
                'created' => Typecho_Date::time(),
                'modified' => Typecho_Date::time(),
                'text' => '<!--markdown-->' . _t('本页面由 Typecho 创建, 这只是个测试页面.'),
                'authorId' => 1,
                'order' => 0,
                'type' => 'page',
                'status' => 'publish',
                'commentsNum' => 0,
                'allowComment' => 1,
                'allowPing' => 1,
                'allowFeed' => 1,
                'parent' => 0
            ])
        );

        // write comment
        $installDb->query(
            $installDb->insert('table.comments')->rows([
                'cid' => 1, 'created' => Typecho_Date::time(),
                'author' => 'Typecho',
                'ownerId' => 1,
                'url' => 'http://typecho.org',
                'ip' => '127.0.0.1',
                'agent' => $options->generator,
                'text' => '欢迎加入 Typecho 大家族',
                'type' => 'comment',
                'status' => 'approved',
                'parent' => 0
            ])
        );
    } catch (Typecho_Db_Exception $e) {
        install_raise_error($e->getMessage());
    }

    $parts = parse_url($options->loginAction);
    $parts['query'] = http_build_query([
            'name'  => $config['userName'],
            'password' => $config['userPassword'],
            'referer' => $options->adminUrl
        ]);
    $loginUrl = Typecho_Common::buildUrl($parts);

    install_success(0, [
        $config['userName'],
        $config['userPassword'],
        Typecho_Widget::widget('Widget_Security')->getTokenUrl($loginUrl, $request->getReferer()),
        $options->siteUrl
    ]);
}

/**
 * dispatch install action
 *
 * @throws Typecho_Exception
 */
function install_dispatch() {
    define('__TYPECHO_INSTALL__', true);

    // disable root url on cli mode
    if (install_is_cli()) {
        define('__TYPECHO_ROOT_URL__', 'http://localhost');
    }

    // init default options
    $options = Typecho_Widget::widget('Widget_Options', install_get_default_options());
    Typecho_Widget::widget('Widget_Init');

    // install finished yet
    if (
        install_check('config')
        && install_check('db_structure')
        && install_check('db_data')
    ) {
        // redirect to siteUrl if not cli
        if (!install_is_cli()) {
            Typecho_Response::getInstance()->redirect($options->siteUrl);
        }

        exit(1);
    }

    if (install_is_cli()) {
        install_step_2_perform();
    } else {
        $request = Typecho_Request::getInstance();
        $response = Typecho_Response::getInstance();
        $step = $request->get('step');

        $action = 1;

        switch (true) {
            case $step == 2:
                if (!install_check('db_structure')) {
                    $action = 2;
                } else {
                    $response->redirect('install.php?step=3');
                }
                break;
            case $step == 3:
                if (install_check('db_structure')) {
                    $action = 3;
                } else {
                    $response->redirect('install.php?step=2');
                }
                break;
            default:
                break;
        }

        $method = 'install_step_' . $action;

        if ($request->isPost() && $action > 1) {
            $method .= '_perform';
            $method();
            exit;
        }
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="<?php _e('UTF-8'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php _e('Typecho 安装程序'); ?></title>
    <link rel="stylesheet" type="text/css" href="<?php $options->adminStaticUrl('css', 'normalize.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?php $options->adminStaticUrl('css', 'grid.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?php $options->adminStaticUrl('css', 'style.css') ?>" />
    <link rel="stylesheet" type="text/css" href="<?php $options->adminStaticUrl('css', 'install.css') ?>" />
    <script src="<?php $options->adminStaticUrl('js', 'jquery.js'); ?>"></script>
</head>
<body>
    <div class="body container">
        <h1><a href="http://typecho.org" target="_blank" class="i-logo">Typecho</a></h1>
        <?php $method(); ?>
    </div>
</body>
</html>
<?php
    }
}

install_dispatch();
