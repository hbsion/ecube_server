<?php
/**
 *--------------------------------------------------------------------------
 * Common Config Setting File.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */

/**
 * PHP Default Settings.
 */
date_default_timezone_set('Asia/Tokyo');

/**
 * Common URL
 */
define('SITE_URL','http://localhost:50080');
define('DEFAULT_ROUTE', 'home');

/**
 * Database Connection Settings
 * @var string
 */
define('DB_ENGINE','mysql');
define('DB_HOST','');
define('DB_NAME','');
define('DB_USER','');
define('DB_PASS','');

/**
 * Mail Connection Settings
 */
define('MAIL_HOST', '');
define('MAIL_NAME', '');
define('MAIL_USER', '');
define('MAIL_PASS', '');
define('MAIL_PORT', '');
define('MAIL_ENCODE', '');
define('MAIL_CHAR', '');
define('MAIL_FROM', '');
