<?php
/**
 *--------------------------------------------------------------------------
 * Route Config Setting File.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
if (!defined('ROOT_PATH'))
{
    die('Access Denied');
}

/**
 * @see .htaccess 未使用: http://www.xxx.com/index.php/home
 * @see .htaccess 　使用: http://www.xxx.com/home
 * @see load::l('router')->add('/[uri path]','[template file name]');
 */
load::l('router')->add('/home', 'home');
