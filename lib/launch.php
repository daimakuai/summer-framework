<?php
defined('IN_FRAME') or exit('Access Denied');

header("Content-type: text/html;charset=utf-8");
PHP_VERSION >= '5.3' && date_default_timezone_set('Asia/Shanghai');
if(PHP_VERSION < '5.3') exit('php version must >5.3 ');




/*  系统默认配置请勿修改  */
//系统根目录
define ('SYS_ROOT', substr(dirname(__FILE__),0,-3));
//APP目录
define ('APP_ROOT', SYS_ROOT.'app/');
//配置目录
define ('CONFIG_ROOT', SYS_ROOT.'config/');
//类包目录
define ('LIB_ROOT', SYS_ROOT.'lib/');
//扩展类包目录
define ('EXT_LIB', LIB_ROOT.'ext_class/');
//网站目录
define ('WEB_ROOT', SYS_ROOT.'public/');
//数据目录
define ('DATA_ROOT', SYS_ROOT.'data/');
//默认类目录
define ('CLASS_NAME', 'class');
//默认方法目录
define ('FUNCTION_NAME', 'func');
//默认文件后缀
define ('PHP_FILE_NAME', 'php');

if ( file_exists(CONFIG_ROOT.'config.php') ){
    require_once CONFIG_ROOT.'config.php';
    if ( file_exists(CONFIG_ROOT.ENVIRONMENT.'.config.php') ){
        require_once CONFIG_ROOT.ENVIRONMENT.'.config.php';
    }else{
        die('not find '.ENVIRONMENT.' config file.');
    }
}else{
    die('not find config file.');
}


/*   默认配置  BEGIN */
//运行环境
defined('ENVIRONMENT')  or define ('ENVIRONMENT', 'dev');
//路由模式 1普通GET 2伪静态 3PATH模式
defined('ROUTE_MODEL')  or define ('ROUTE_MODEL', '2');
//伪静态后缀
defined('URL_HTML_SUFFIX')  or define ('URL_HTML_SUFFIX','html|htm|jsp');
//默认模板后缀
defined('TEMPLATE_SUFFIX')  or define ('TEMPLATE_SUFFIX','html');
//默认模块
defined('DEFAULT_MODULE')  or define("DEFAULT_MODULE",'web');
//默认ACTION
defined('DEFAULT_ACTION')  or define("DEFAULT_ACTION",'index');
//默认方法
defined('DEFAULT_DO')  or define("DEFAULT_DO",'doIndex');
//默认数据库
defined('DB_TYPE')  or define("DB_TYPE",'mysql');

/* 默认配置 END */

if(ENVIRONMENT=='dev')
    error_reporting(E_ALL);
else
    error_reporting(E_ERROR | E_PARSE | E_CORE_ERROR |E_COMPILE_ERROR | E_USER_ERROR );




if(ROUTE_MODEL>=2){
    $_SERVER['PATH_INFO'] = preg_replace('/\.('.URL_HTML_SUFFIX.')$/i','',$_SERVER['PATH_INFO']);
    $routes = explode("/",$_SERVER['PATH_INFO']);
    $module = isset($routes[1]) ? $routes[1] : DEFAULT_MODULE;
    $action = isset($routes[2]) ? $routes[2] : DEFAULT_ACTION;
    $do     = isset($routes[3]) ? $routes[3] : DEFAULT_DO;
}else{
    $module    = isset($_GET['m']) ? $_GET['m'] : DEFAULT_MODULE;
    $action    = isset($_GET['c']) ? $_GET['c'] : DEFAULT_ACTION;
    $do        = isset($_GET['d']) ? $_GET['d'] : DEFAULT_DO;
}

define('MODULE',$module);
define('ACTION',$action);
define('DO',$do);

//程序运行开始时间
$start_time = microtime(true);
define('START_TIME',$start_time);

require_once LIB_ROOT.CLASS_NAME.'/loader.'.CLASS_NAME.'.'.PHP_FILE_NAME;
loader::_load('global',FUNCTION_NAME);
loader::_fun('global');
loader::_class('template');
loader::_class(DB_TYPE);
loader::_run($module,$action,$do);

