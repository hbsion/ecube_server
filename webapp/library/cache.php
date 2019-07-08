<?php
/**
 *--------------------------------------------------------------------------
 * Cash Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class Cache
{
    /**
     * Cache Construct.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        if (!function_exists('apc_fetch'))
        {
            die("PECL APC Not Installed, Cache Library cannot be use");
        }
    }

    /**
     * Cache Get.
     *
     * @param $key
     * @return $value
     */
    public function get($key)
    {
        if (empty($key))
        {
            return null;
        }
        else
        {
            $value = apc_fetch($key);
            return $value;
        }
    }

    /**
     * Cache Set.
     *
     * @param $key
     * @param $value
     * @return true|false
     */
    public function set($key = null, $value = null)
    {
        if (empty($key) || $value === null)
        {
            return false;
        }
        apc_store($key, $value);
        return true;
    }

    /**
     * Cache Clear.
     *
     * @param void
     * @return void
     */
    public function clear()
    {
        return apc_clear_cache('user');
    }

    /**
     * Cache Infomation.
     *
     * @param void
     * @return void
     */
    public function info()
    {
        return apc_cache_info('user');
    }
}
?>
