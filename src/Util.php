<?php
/**
 * Created by PhpStorm.
 * User: loconox
 * Date: 03/11/2017
 * Time: 08:51
 */

namespace App;


class Util
{
    public static function slugify($text)
    {
        $text = trim($text);
        // this code is for BC
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        /*if (function_exists('iconv')) {
            $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        }*/
        $text = transliterator_transliterate("Latin-ASCII", $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        return $text;
    }
}