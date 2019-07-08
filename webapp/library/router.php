<?php
/**
 *--------------------------------------------------------------------------
 * Router Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class Router
{
    protected $routes = array();
    protected $regexes = array();
    public $uri_string;
    public $segments = array();
    public $reg_segments = array();
    public $permitted_uri_chars = 'a-z 0-9~%.:_\-';

    /**
     * Route Construct.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->fetch_uri_string();
        $this->explode_segments();
    }

    /**
     * Route Construct.
     *
     * @param $path Route Path
     * @param $view View
     * @return Output
     */
    public function add($path, $view)
    {
        $this->routes[] = array('path' => $path, 'view' => $view);
        $this->regexes[]= "#^{$path}\$#";
    }

    /**
     * Route Fetch Uri String.
     *
     * @param void
     * @return void
     */
    protected function fetch_uri_string()
    {
        $path = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
        // 空でなく、かつ、自身のパスでもない
        if (trim($path, '/') != '' && $path != "/".$_SERVER['SCRIPT_NAME'])
        {
            $this->uri_string = $path;
            return;
        }
        $path = str_replace($_SERVER['SCRIPT_NAME'], '', (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO'));

        if (trim($path, '/') != '' && $path != "/".$_SERVER['SCRIPT_NAME'])
        {
            $this->uri_string = $path;
            return;
        }
        $this->uri_string = '/';
    }

    /**
     * Route Explode Segments.
     *
     * @param void
     * @return void
     */
    protected function explode_segments()
    {
        foreach(explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $this->uri_string)) as $val)
        {
            // セキュリティ フィルター セグメント
            $val = trim($this->filter_uri($val));
            if ($val != '')
            {
                $this->segments[] = $val;
            }
        }
    }

    /**
     * Route Filter Uri.
     *
     * @param $str
     * @return $str Replace
     */
    protected function filter_uri($str)
    {
        if ($str != '' && $this->permitted_uri_chars != '')
        {
            if ( ! preg_match("|^[".str_replace(array('\\-', '\-'), '-', preg_quote($this->permitted_uri_chars, '-'))."]+$|i", $str))
            {
                die('The URI you submitted has disallowed characters.');
            }
        }
        // プログラムの文字を Entities に変換
        $bad = array('$', '(', ')', '%28', '%29');
        $good = array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');

        return str_replace($bad, $good, $str);
    }

    /**
     * Route Run.
     *
     * @param void
     * @return void
     */
    public function run()
    {
        foreach ($this->regexes as $ind => $regex)
        {
            if (preg_match($regex, $this->uri_string, $arguments))
            {
                array_shift($arguments);
                $def = $this->routes[$ind];
                if (file_exists(VIEW_DIR.$def['view'].'.php'))
                {
                    $this->reg_segments = load::l('input')->clean_input_data($arguments);
                    load::v($def['view']);
                    return;
                }
                else
                {
                    die('Could not call ' . json_encode($def) . " for route {$regex}");
                }
            }
        }
        die("Could not find route {$this->uri_string} from {$_SERVER['REQUEST_URI']}");
    }
}
