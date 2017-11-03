<?php
namespace App\Twig;

use App\Util;

class Extension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            new \Twig_Filter('slugify', [$this, 'slugify'])
        ];
    }

    public function slugify($str)
    {
        return Util::slugify($str);
    }

}