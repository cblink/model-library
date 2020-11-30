<?php


/**
 * 字符处理
 *
 * @param $string
 * @param string $format
 * @return false|string
 */
function strtodate($string, $format = 'Y-m-d H:i:s')
{
    return date($format, strtotime($string));
}