<?php
//  Constants  ---------------------------------------------------------------
define('IN_CMS', true);

define('CMS_VERSION', '0.8.3.1');
define('CMS_ROOT', __DIR__.'/../');
define('DS', DIRECTORY_SEPARATOR);
define('CORE_ROOT', CMS_ROOT . DS . 'wolf');
define('PLUGINS_ROOT', CORE_ROOT . DS . 'plugins');
define('APP_PATH', CORE_ROOT . DS . 'app');

require_once(CORE_ROOT . DS . 'utils.php');

$configFile = CMS_ROOT . DS . 'config.php';
require_once($configFile);

// if you have installed wolf and see this line, you can comment it or delete it :)
if (!defined('DEBUG')) {
    header('Location: wolf/install/');
    exit();
}

$url = URL_PUBLIC;

// Figure out what the public path is based on URL_PUBLIC.
// @todo improve
$changedurl = str_replace('//', '|', URL_PUBLIC);
$lastslash = strpos($changedurl, '/');
if (false === $lastslash) {
    define('PATH_PUBLIC', '/');
} else {
    define('PATH_PUBLIC', substr($changedurl, $lastslash));
}

// Determine path for backend check
$adminCheck = urldecode(filter_var($_SERVER['REQUEST_URI'] ?? ''));

// Are we in frontend or backend?
if (startsWith($adminCheck, '/' . ADMIN_DIR) || startsWith($adminCheck, '/' . ADMIN_DIR)) {
    define('CMS_BACKEND', true);
    if (defined('USE_HTTPS') && USE_HTTPS) {
        $url = str_replace('http://', 'https://', $url);
    }
    define('BASE_URL', $url . (endsWith($url, '/') ? '' : '/') . ADMIN_DIR . (endsWith(ADMIN_DIR, '/') ? '' : '/'));
    define('BASE_PATH', PATH_PUBLIC . (endsWith($url, '/') ? '' : '/') . ADMIN_DIR . (endsWith(ADMIN_DIR, '/') ? '' : '/'));
} else {
    define('BASE_URL', URL_PUBLIC . (endsWith(URL_PUBLIC, '/') ? '' : '/'));
    define('BASE_PATH', PATH_PUBLIC . (endsWith(PATH_PUBLIC, '/') ? '' : '/'));
}

// Alias for backward 

define('PLUGINS_PATH', PATH_PUBLIC . 'wolf/plugins/');

if (!defined('ASSETS_ROOT')) {
    define('ASSETS_ROOT', __DIR__.'/assets/');
}

if (!defined('ASSETS_PATH')) {
    define('ASSETS_PATH', PATH_PUBLIC . 'assets/');
}

if (!defined('PLUGINS_ASSETS_PATH')) {
    define('PLUGINS_ASSETS_PATH', ASSETS_PATH . 'plugins/');
}


if (!defined('THEMES_ROOT')) {
    define('THEMES_ROOT', ASSETS_ROOT.'themes/');
}
if (!defined('THEMES_PATH')) {
    define('THEMES_PATH', PATH_PUBLIC . 'assets/themes/');
}

if (!defined('ICONS_PATH')) {
    define('ICONS_PATH', ASSETS_PATH.'icons/');
}

//  Init  --------------------------------------------------------------------

define('SESSION_LIFETIME', 3600);
define('REMEMBER_LOGIN_LIFETIME', 1209600); // two weeks

define('DEFAULT_CONTROLLER', 'page');
define('DEFAULT_ACTION', 'index');

require CORE_ROOT . '/Framework.php';

AutoLoader::register();

AutoLoader::addFolder([
    APP_PATH . '/models',
    APP_PATH . '/controllers',
]);

try {
    $__CMS_CONN__ = new PDO(DB_DSN, DB_USER, DB_PASS);
} catch (PDOException $error) {
    die('DB Connection failed: ' . $error->getMessage());
}

$driver = $__CMS_CONN__->getAttribute(PDO::ATTR_DRIVER_NAME);

if ($driver === 'mysql') {
    $__CMS_CONN__->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
}

if ($driver === 'sqlite') {
    // Adding date_format function to SQLite 3 'mysql date_format function'
    if (!function_exists('mysql_date_format_function')) {

        function mysql_function_date_format($date, $format) {
            return strftime($format, strtotime($date));
        }

    }
    $__CMS_CONN__->sqliteCreateFunction('date_format', 'mysql_function_date_format', 2);
}

Record::connection($__CMS_CONN__);
Record::getConnection()->exec("set names 'utf8'");

Setting::init();

use_helper('I18n');
AuthUser::load();
if (AuthUser::isLoggedIn()) {
    I18n::setLocale(AuthUser::getRecord()->language);
} else {
    I18n::setLocale(Setting::get('language'));
}

// Only add the cron web bug when necessary
if (defined('USE_POORMANSCRON') && USE_POORMANSCRON && defined('POORMANSCRON_INTERVAL')) {
    Observer::observe('page_before_execute_layout', 'run_cron');

    function run_cron() {
        $cron = Cron::findByIdFrom('Cron', '1');
        $now = time();
        $last = $cron->getLastRunTime();

        if ($now - $last > POORMANSCRON_INTERVAL) {
            echo $cron->generateWebBug();
        }
    }

}

Plugin::init();
Flash::init();

// Setup admin routes
$adminRoutes = [
    '/' . ADMIN_DIR => Setting::get('default_tab'),
    '/' . ADMIN_DIR . '/' => Setting::get('default_tab'),
    '/' . ADMIN_DIR . '/:all' => '$1',
];

Dispatcher::addRoute($adminRoutes);

require APP_PATH . '/main.php';

ob_start();
main($adminCheck);
echo ob_get_clean();
exit;

