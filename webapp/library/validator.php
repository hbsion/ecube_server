<?php
/**
 *--------------------------------------------------------------------------
 * Validates input against certain criteria.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0.0
 * @copyright 1985-2017 Copyright (c) USEN
 */

class Validator
{
    /**
     * Error Message.
     *
     * @var string
     */
    protected $message;

    /**
     * Required for Check
     *
     * @param string $str
     * @return boolean Validation
     */
    function required($str)
    {
        if ($str === null || strcmp($str, "") === 0)
        {
            return false;
        }
        return true;
    }

    /**
     * 半角英数のみ
     *
     * @param string $str
     * @return boolean Validation
     */
    function alphaNumeric($str, $max = null, $min = null)
    {
        if (!preg_match("/^[a-zA-Z0-9]+$/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * 半角数字のみ
     *
     * @param string $str
     * @return boolean Validation
     */
    function numeric($str)
    {
        if (!preg_match("/^[0-9]+$/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * 半角英字のみ
     *
     * @param string $str
     * @return boolean Validation
     */
    function alpha($str, $max = null, $min = null)
    {
        if (!preg_match("/^[a-zA-Z]+$/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * 全角のみ
     *
     * @param string $str
     * @return boolean Validation
     */
    function zenkaku($str, $max = null, $min = null)
    {
        if (!preg_match("/[^\x01-\x7E]+$/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * 全角カナのみ
     *
     * @param string $str
     * @return boolean Validation
     */
    function zenkana($str, $max = null, $min = null)
    {
        if (!preg_match("/^[ァ-ヶー]+$/u", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * Phone number（ハイフン無しの6～9桁）
     *
     * @param string $str
     * @return boolean Validation
     */
    function phone($str)
    {
        if (!preg_match("/^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * Email Validate.
     *
     * @param string $str
     * @return boolean Validate
     */
    function email($str)
    {
        if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $str))
        {
            return false;
        }
        $str_array = explode("@", $str);
        $local_array = explode(".", $str_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++)
        {
            if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i]))
            {
                return false;
            }
        }
        if (!preg_match("/^\[?[0-9\.]+\]?$/", $str_array[1]))
        {
            $domain_array = explode(".", $str_array[1]);
            if (sizeof($domain_array) < 2)
            {
                return false;
            }
            for ($i = 0; $i < sizeof($domain_array); $i++)
            {
                if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i]))
                {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Credit card（ハイフン無しの13～16桁）
     *
     * @param string $type VISA:1|Master:2|JCB:3|American:4|Diners:5
     * @return boolean Validation
     */
    function creditcd($param)
    {
        switch ($param) {
            case !preg_match("/^4[0-9]{12}(?:[0-9]{3})?$/", $param):
                return 'visa';
                break;
            case !preg_match("/^5[1-5][0-9]{14}$/", $param):
                return 'master';
                break;
            case !preg_match("/^(?:2131|1800|35\d{3})\d{11}$/", $param):
                return 'jcb';
                break;
            case !preg_match("/^3[47][0-9]{13}$/", $param):
                return 'american';
                break;
            case !preg_match("/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/", $param):
                return 'diners';
                break;
            case !preg_match("/^6011[0-9]{12}$/", $param):
                return 'discover';
                break;
            default: // All
                if (!preg_match("/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6011[0-9]{12}|3(?:0[0-5]|[68][0-9])[0-9]{11}|3[47][0-9]{13}|(?:2131|1800|35[0-9]{3})[0-9]{11})$/", $param))
                {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Only alpha-numeric or alphabetic（半角英文字1文字以上 or 英文字のみ）
     *
     * @param string $str
     * @return boolean Validation
     */
    function numalp($str)
    {
        if (!preg_match("/([0-9].*[a-zA-Z]|[a-zA-Z].*[0-9]|[a-zA-Z])/", $str))
        {
            return false;
        }
        return true;
    }

    /**
     * 日付（YYYY-MM-DD）
     *
     * @param string $str
     * @return boolean Validation
     */
    function dateFormat($str)
    {
        if (!preg_match("/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/", $str))
        {
            return false;
        }
        return true;
    }
}
