<?php
/**
 * 单入口系统
 */
 
//error_reporting(0);
@set_time_limit(240);
//@ini_set("memory_limit",'-1');
define('APP_DEBUG', true);				//是否开启调试模式
define('UPLOAD_PATH','./Uploads/');
define('THINK_PATH', './Core/ThinkPHP/');
define('RUNTIME_PATH','./Temp/');
define('TMPL_PATH','./Template/');
define('APP_PATH', './Core/');
define('APP_NAME', 'cms');
require(THINK_PATH."/ThinkPHP.php");	//加载框架入口文件