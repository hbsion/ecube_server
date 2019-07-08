<?php
/**
 *--------------------------------------------------------------------------
 * Session Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class Session
{
    /**
     * Session Construct.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        @session_start();
    }

    /**
     * Session Set.
     *
     * @param $key Session Key
     * @param $value Session Value
     * @param $overwrite
     * @return Session
     */
    public function set($key, $value = NULL, $overwrite = TRUE)
    {
        if ($overwrite)
        {
            $_SESSION[$key] = $value;
        }
        return $_SESSION[$key];
    }

    /**
     * Session Get.
     *
     * @param $key Session Key
     * @param $prefix
     * @return Session
     */
    public function get($key, $prefix = false)
    {
        if ($prefix)
        {
            $tmp = array();
            foreach ($_SESSION as $k => $v)
            {
                if (strpos($k, $key) === 0)
                {
                    $tmp[$k] = $_SESSION[$k];
                }
            }
            return $tmp;
        }
        return $_SESSION[$key];
    }

    /**
     * Session Set Confirmation.
     *
     * @param $key Session Key
     * @return Session
     */
    public function is_set($key)
    {
        if (!is_array($key))
        {
            $key = func_num_args() > 0 ? func_get_args() : array($key);
        }
        else
        {
            $key = array_values($key);
        }

        foreach ($key as $v)
        {
            if (!isset($_SESSION[$v]) || empty($_SESSION[$v]))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Session Clear.
     *
     * @param $key Session Key
     * @param $prefix
     * @return Session Clear
     */
    public function clear($key, $prefix = false)
    {
        if ($prefix)
        {
            foreach ($_SESSION as $k => $v)
            {
                if (strpos($k, $key) === 0)
                {
                    unset($_SESSION[$k]);
                }
            }
            return;
        }
        unset($_SESSION[$key]);
        return;
    }

    /**
     * Session Destroy.
     *
     * @param $key Session Key
     * @param $prefix
     * @return Session Destroy
     */
    public function end()
    {
        $_SESSION = array();
        session_destroy();
    }
}
