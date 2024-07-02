<?php
// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();

spl_autoload_unregister(array('YiiBase','autoload'));
require Yii::getPathOfAlias('application.vendor').DIRECTORY_SEPARATOR.'autoload.php';
spl_autoload_register(array('YiiBase','autoload'));
