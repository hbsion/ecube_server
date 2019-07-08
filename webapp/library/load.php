<?php
/**
 *--------------------------------------------------------------------------
 * Load Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class Load
{
    public static $instance = array();

    /**
     * File Load Construct.
     *
     * @param void
     * @return void
     */
    public function __construct(){}

    /**
     * File Load Controller.
     *
     * @param $name
     * @param $args
     * @return Instance
     */
    public static function c($name, $args = array())
    {
        if (!file_exists(CONTROLLER_DIR.$name.".php")) {
            die("$name - File Not Found");
        }
        require_once LIBRARY_DIR."controller.php";
        require_once CONTROLLER_DIR.$name.".php";
        return self::get_instance($name, $args);
    }

    /**
     * File Load Libraries.
     *
     * @param $name
     * @param $args
     * @return Instance
     */
    public static function l($name, $args = array())
    {
        if (!file_exists(LIBRARY_DIR.$name.".php")) {
            die("$name - File Not Found");
        }
        require_once LIBRARY_DIR.$name.".php";
        return self::get_instance($name,$args);
    }

    /**
     * File Load Models.
     *
     * @param $name
     * @param $args
     * @return Instance
     */
    public static function m($name, $args = array())
    {
        if (!file_exists(MODEL_DIR.$name.".php")) {
            die("$name - File Not Found");
        }
        require_once MODEL_DIR.$name.".php";
        return self::get_instance($name,$args);
    }

    /**
     * File Load Views.
     *
     * @param $name
     * @param $args
     * @return Instance
     */
    public static function v($name, $args = array())
    {
        if (!file_exists(VIEW_DIR.$name.".php")) {
            die("$name - File Not Found");
        }
        require_once VIEW_DIR.$name.".php";
    }

    /**
     * File Load Get Instance.
     *
     * @param $name
     * @param $args
     * @return Instance
     */
    public static function get_instance($name, $args = array())
    {
        if (class_exists($name) && !array_key_exists($name, self::$instance)) {
            self::$instance[$name] = new $name($args);
            return self::$instance[$name];
        } elseif (class_exists($name) && array_key_exists($name, self::$instance)) {
            return self::$instance[$name];
        } else {
            return NULL;
        }
    }
}
