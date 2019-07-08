<?php
/**
 *--------------------------------------------------------------------------
 * Input Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
Class Input
{
    public $use_xss_clean = TRUE;

    /**
     * Xss Hash 初期化
     */
    protected $xss_hash = '';

    /* 文字列の自動置換を拒否設定 */
    protected $never_allowed_str = array(
        'document.cookie' => '[removed]',
        'document.write'  => '[removed]',
        '.parentNode'     => '[removed]',
        '.innerHTML'      => '[removed]',
        'window.location' => '[removed]',
        '-moz-binding'    => '[removed]',
        '<!--'            => '&lt;!--',
        '-->'             => '--&gt;',
        '<![CDATA['       => '&lt;![CDATA['
    );

    /* 正規表現は置換対象から除外 */
    protected $never_allowed_regex = array(
        "javascript\s*:"            => '[removed]',
        "expression\s*(\(|&\#40;)"  => '[removed]', // CSS and IE
        "vbscript\s*:"              => '[removed]', // IE, surprise!
        "Redirect\s+302"            => '[removed]'
    );

    /**
     * Input From Construct.
     *
     * @param void
     * @return void
     */
    public function __construct()
    {
        $this->sanitize();
    }

    /**
     * Input From Sanitize.
     *
     * @param void
     * @return void
     */
    protected function sanitize()
    {
        if (isset($_GET))
        {
            $_GET = $this->clean_input_data($_GET);
        }
        if (isset($_POST))
        {
            $_POST = $this->clean_input_data($_POST);
        }
        /* 入力値のエスケープ後、全ての cookie に html_entity_decode() を使用しての対策必須 */
        // if (isset($_COOKIE))
        // {
        //     $_COOKIE = $this->clean_input_data($_COOKIE);
        // }
    }

    /**
     * Input From URL Get Parameter.
     *
     * @param $key
     * @return value
     */
    public function get($key)
    {
        return $_GET[$key];
    }

    /**
     * Input From URL Post Parameter.
     *
     * @param $key
     * @return value
     */
    public function post($key)
    {
        return $_POST[$key];
    }

    /**
     * Input From Clean Data.
     *
     * @param $str Original
     * @return $str Html Entities
     */
    public function clean_input_data($str)
    {
        if (is_array($str))
        {
            $new_array = array();
            foreach ($str as $key => $val)
            {
                $new_array[$this->clean_input_keys($key)] = $this->clean_input_data($val);
            }
            return $new_array;
        }
        // マジッククオートは、一貫性を持たせる場合、/ を除外
        if (get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        // 入力値データをフィルター処理
        if ($this->use_xss_clean === TRUE)
        {
            $str = $this->xss_clean($str);
        }
        // 改行標準化
        if (strpos($str, "\r") !== FALSE)
        {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }
        $str = htmlspecialchars($str, ENT_QUOTES);

        return $str;
    }

    /**
     * Input From Clean Key.
     *
     * @param $str Original
     * @return $str Allowed Key Characters
     */
    protected function clean_input_keys($str)
    {
        if (!preg_match("/^[a-z0-9:_\/-]+$/i", $str))
        {
            die('Disallowed Key Characters.');
        }

        return $str;
    }

    /**
     * Input From Clean XSS.
     *
     * @param $str Original
     * @param $is_image
     * @return $str XSS is cleared
     */
    function xss_clean($str, $is_image = FALSE)
    {
        if (is_array($str))
        {
            while (list($key) = each($str))
            {
                $str[$key] = $this->xss_clean($str[$key]);
            }

            return $str;
        }

        $str = preg_replace('|\&([a-z\_0-9]+)\=([a-z\_0-9]+)|i', $this->xss_hash()."\\1=\\2", $str);
        $str = preg_replace('#(&\#?\{\}[0-9a-z]{2,})([\x00-\x20])*;?#i', "\\1;\\2", $str);
        $str = preg_replace('#(&\#x?)([0-9A-F]+);?#i',"\\1\\2;",$str);
        $str = str_replace($this->xss_hash(), '&', $str);
        $str = rawurldecode($str);
        $str = preg_replace_callback("/[a-z]+=([\'\"]).*?\\1/si", array($this, '_convert_attribute'), $str);
        $str = preg_replace_callback("/<\w+.*?(?=>|<|$)/si", array($this, '_html_entity_decode_callback'), $str);
        $str = $this->_remove_invisible_characters($str);
        if (strpos($str, "\t") !== FALSE)
        {
            $str = str_replace("\t", ' ', $str);
        }
        $converted_string = $str;

        foreach ($this->never_allowed_str as $key => $val)
        {
            $str = str_replace($key, $val, $str);
        }
        foreach ($this->never_allowed_regex as $key => $val)
        {
            $str = preg_replace("#".$key."#i", $val, $str);
        }

        if ($is_image === TRUE)
        {
            $str = preg_replace('/<\?(php)/i', "&lt;?\\1", $str);
        }
        else
        {
            $str = str_replace(array('<?', '?'.'>'),  array('&lt;?', '?&gt;'), $str);
        }

        $words = array('javascript', 'expression', 'vbscript', 'script', 'applet', 'alert', 'document', 'write', 'cookie', 'window');
        foreach ($words as $word)
        {
            $temp = '';

            for ($i = 0, $wordlen = strlen($word); $i < $wordlen; $i++)
            {
                $temp .= substr($word, $i, 1)."\s*";
            }
            $str = preg_replace_callback('#('.substr($temp, 0, -3).')(\W)#is', array($this, '_compact_exploded_words'), $str);
        }

        do
        {
            $original = $str;
            if (preg_match("/<a/i", $str))
            {
                $str = preg_replace_callback("#<a\s+([^>]*?)(>|$)#si", array($this, '_js_link_removal'), $str);
            }
            if (preg_match("/<img/i", $str))
            {
                $str = preg_replace_callback("#<img\s+([^>]*?)(\s?/?>|$)#si", array($this, '_js_img_removal'), $str);
            }
            if (preg_match("/script/i", $str) OR preg_match("/xss/i", $str))
            {
                $str = preg_replace("#<(/*)(script|xss)(.*?)\>#si", '[removed]', $str);
            }
        }
        while($original != $str);

        unset($original);

        $event_handlers = array('[^a-z_\-]on\w*','xmlns');

        if ($is_image === TRUE)
        {
            unset($event_handlers[array_search('xmlns', $event_handlers)]);
        }
        $str = preg_replace("#<([^><]+?)(".implode('|', $event_handlers).")(\s*=\s*[^><]*)([><]*)#i", "<\\1\\4", $str);

        $naughty = 'alert|applet|audio|basefont|base|behavior|bgsound|blink|body|embed|expression|form|frameset|frame|head|html|ilayer|iframe|input|isindex|layer|link|meta|object|plaintext|style|script|textarea|title|video|xml|xss';

        $str = preg_replace_callback(
            '#<(/*\s*)('.$naughty.')([^><]*)([><]*)#is',
            array($this, '_sanitize_naughty_html'),
            $str
        );
        $str = preg_replace(
            '#(alert|cmd|passthru|eval|exec|expression|system|fopen|fsockopen|file|file_get_contents|readfile|unlink)(\s*)\((.*?)\)#si',
            "\\1\\2&#40;\\3&#41;",
            $str
        );

        foreach ($this->never_allowed_str as $key => $val)
        {
            $str = str_replace($key, $val, $str);
        }
        foreach ($this->never_allowed_regex as $key => $val)
        {
            $str = preg_replace("#".$key."#i", $val, $str);
        }

        if ($is_image === TRUE)
        {
            if ($str == $converted_string)
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }

        return $str;
    }

    /**
     * Input From Clean XSS Hash.
     *
     * @param void
     * @return XSS Hash
     */
    function xss_hash()
    {
        if ($this->xss_hash == '')
        {
            if (phpversion() >= 4.2)
            {
                mt_srand();
            }
            else
            {
                mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);
            }
            $this->xss_hash = md5(time() + mt_rand(0, 1999999999));
        }

        return $this->xss_hash;
    }

    /**
     * Input From Clean Invisible.
     *
     * @param $str Original
     * @return $str Remove Invisible Characters
     */
    function _remove_invisible_characters($str)
    {
        static $non_displayables;

        if (!isset($non_displayables))
        {
            // 改行を除く全ての制御文字(dec 10)、キャリッジ・リターン(dec 13)、水平タブ(dec 09)
            $non_displayables = array(
                '/%0[0-8bcef]/',            // url encoded 00-08, 11, 12, 14, 15
                '/%1[0-9a-f]/',             // url encoded 16-31
                '/[\x00-\x08]/',            // 00-08
                '/\x0b/', '/\x0c/',         // 11, 12
                '/[\x0e-\x1f]/'             // 14-31
            );
        }

        do
        {
            $cleaned = $str;
            $str = preg_replace($non_displayables, '', $str);
        }
        while ($cleaned != $str);

        return $str;
    }

    /**
     * Input From Preg Replace.
     *
     * @param $matches Word
     * @return $str Replace Word
     */
    function _compact_exploded_words($matches)
    {
        return preg_replace('/\s+/s', '', $matches[1]).$matches[2];
    }

    /**
     * Input From Sanitization Naughty.
     *
     * @param $matches
     * @return $str
     */
    function _sanitize_naughty_html($matches)
    {
        // ’<’ エンコード
        $str = '&lt;'.$matches[1].$matches[2].$matches[3];
        // キャプチャを開く or 再帰的ベクトルを防ぐ為 ’>’、'<' をエンコード
        $str .= str_replace(array('>', '<'), array('&gt;', '&lt;'), $matches[4]);

        return $str;
    }

    /**
     * Input From Javascript Remove Links.
     *
     * @param $match Word
     * @return $match Replace Word
     */
    function _js_link_removal($match)
    {
        $attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));

        return str_replace($match[1], preg_replace("#href=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
    }

    /**
     * Input From Javascript Remove Images.
     *
     * @param $match Word
     * @return $match Replace Word
     */
    function _js_img_removal($match)
    {
        $attributes = $this->_filter_attributes(str_replace(array('<', '>'), '', $match[1]));

        return str_replace($match[1], preg_replace("#src=.*?(alert\(|alert&\#40;|javascript\:|charset\=|window\.|document\.|\.cookie|<script|<xss|base64\s*,)#si", "", $attributes), $match[0]);
    }

    /**
     * Input From Html Tags Convert Attributes.
     *
     * @param $match Word
     * @return $match Replace Word
     */
    function _convert_attribute($match)
    {
        return str_replace(array('>', '<', '\\'), array('&gt;', '&lt;', '\\\\'), $match[0]);
    }

    /**
     * Input From Html Characters Decode Callback.
     *
     * @param $match Word
     * @return $match Replace Word
     */
    function _html_entity_decode_callback($match)
    {
        $CFG =& load_class('Config');
        $charset = $CFG->item('charset');

        return $this->_html_entity_decode($match[0], strtoupper($charset));
    }

    /**
     * Input From Html Characters Decode.
     *
     * @param $str Word
     * @param $charset UTF-8
     * @return $str Replace Word
     */
    function _html_entity_decode($str, $charset = 'UTF-8')
    {
        if (stristr($str, '&') === FALSE)
        {
            return $str;
        }

        if (function_exists('html_entity_decode')
        && (strtolower($charset) != 'utf-8' OR version_compare(phpversion(), '5.0.0', '>=')))
        {
            $str = html_entity_decode($str, ENT_COMPAT, $charset);
            $str = preg_replace('~&#x(0*[0-9a-f]{2,5})~ei', 'chr(hexdec("\\1"))', $str);

            return preg_replace('~&#([0-9]{2,4})~e', 'chr(\\1)', $str);
        }

        // 数値 Entities
        $str = preg_replace('~&#x(0*[0-9a-f]{2,5});{0,1}~ei', 'chr(hexdec("\\1"))', $str);
        $str = preg_replace('~&#([0-9]{2,4});{0,1}~e', 'chr(\\1)', $str);

        // Literal Entities - 若干遅くなるので、別チェック処理
        if (stristr($str, '&') === FALSE)
        {
            $str = strtr($str, array_flip(get_html_translation_table(HTML_ENTITIES)));
        }

        return $str;
    }

    /**
     * Input From Html Filter Attributes.
     *
     * @param $str Word
     * @return $out Filter
     */
    function _filter_attributes($str)
    {
        $out = '';

        if (preg_match_all('#\s*[a-z\-]+\s*=\s*(\042|\047)([^\\1]*?)\\1#is', $str, $matches))
        {
            foreach ($matches[0] as $match)
            {
                $out .= preg_replace("#/\*.*?\*/#s", '', $match);
            }
        }

        return $out;
    }
}
