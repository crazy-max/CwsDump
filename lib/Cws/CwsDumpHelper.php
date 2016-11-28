<?php

/**
 * CwsDumpHelper.
 *
 * A simple helper for CwsDump
 *
 * @author Cr@zy
 * @copyright 2013-2016, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 *
 * @link https://github.com/crazy-max/CwsDump
 */
use Cws\CwsDump;

function cwsDump($var, $echo = true)
{
    $result = call_user_func(array(new CwsDump(), 'dump'), $var);
    if ($echo) {
        echo $result;

        return;
    }

    return $result;
}
