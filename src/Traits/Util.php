<?php


namespace App\Traits;


trait Util
{

    public function slugify($text)
    {

        $before = array('ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç','Ş');
        $after = array('i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 'o', 'c', 's');

        $clean = str_replace($before, $after, $text);
        $clean = preg_replace('/[^a-zA-Z0-9 ]/', '', $clean);
        $clean = preg_replace('!\s+!', '-', $clean);
        $clean = strtolower(trim($clean, '-'));

        return $clean;

    }

}