<?php
/**
 *--------------------------------------------------------------------------
 * Initial Route Settiong File.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
 define('ROOT_PATH', dirname(realpath(__DIR__)).'/');

 define('CONTROLLER_DIR', ROOT_PATH.'controllers/');
 define('MODEL_DIR', ROOT_PATH.'models/');
 define('VIEW_DIR', ROOT_PATH.'views/');
 define('LIBRARY_DIR', ROOT_PATH.'library/');

 /**
  * Config Load Settings.
  */
 if (file_exists(ROOT_PATH.'config-dev.php'))
 {
     include ROOT_PATH.'config-dev.php';
 }
 else {
     include ROOT_PATH.'config.php';
 }

 /**
  * File Load Settings.
  */
 include LIBRARY_DIR.'load.php';

/**
 * Session Load Settings.
 */
load::l('session');

/**
 * Input From Sanitization.
 */
load::l('input');

/**
 * Label Include.
 */
load::l('label');

/**
 * Message Include.
 */
load::l('message');

/**
 * Route Path Default.
 */
if (!defined('DEFAULT_ROUTE') || DEFAULT_ROUTE == '') {
    die('Default Route is Missing');
}
load::l('router')->add('/',DEFAULT_ROUTE);

/**
 * Route Path Add.
 */
include ROOT_PATH."routes.php";

load::l('router')->run();
