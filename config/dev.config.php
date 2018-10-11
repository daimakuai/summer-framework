<?php
defined('IN_FRAME') or exit('Access Denied');

/* 环境变量配置信息 */

define("DB_TYPE",'mysql');

//路由模式 1普通GET 2伪静态 3PATH模式
define ('ROUTE_MODEL', '1');
//伪静态后缀
define ('URL_HTML_SUFFIX','html|htm|jsp');
//默认模板后缀
define ('TEMPLATE_SUFFIX','html');

//数据库配置信息
define("CON_DB_HOST",'localhost');
define("CON_DB_PORT",'3306');
define("CON_DB_USER",'root');
define("CON_DB_PASS",'root');
define("CON_DB_NAME",'myframe');
define("CON_DB_PRE",'my_');
define("CON_DB_CHARSET",'utf8');
