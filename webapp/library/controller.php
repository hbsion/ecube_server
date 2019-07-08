<?php
/**
 *--------------------------------------------------------------------------
 * Controller Class.
 *--------------------------------------------------------------------------
 * @author    te-koyama
 * @version   v1.0
 * @copyright 1985-2017 Copyright (c) USEN
 */
abstract Class Controller
{
    protected $vars = array();

    /**
     * Controller Get Output.
     *
     * @param void
     * @return void
     */
    public function getOutput()
    {
        if ($idx = @func_get_arg(0))
        {
            return isset($this->vars[$idx]) ? $this->vars[$idx] : $this->vars;
        }

        return $this->vars;
    }

    /**
     * Controller Set Output.
     *
     * @param $key
     * @param $value
     * @return void
     */
    protected function setOutput($key, $value = NULL)
    {
        if (is_array(func_get_arg(0)))
        {
            foreach (func_get_arg(0) as $key => $value)
            {
                $this->vars[$key] = $value;
            }
        }
        else
        {
            $this->vars[$key] = $value;
        }
    }
}
