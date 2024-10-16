<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 07.06.2018
 * Time: 12:36
 */

namespace app\modules\components;


class Utils
{
    public static function array_filter_key(array $array, array $allowed): array
    {
        return array_filter(
            $array,
            static function ($key) use ($allowed) {
                return in_array($key, $allowed, true);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function merge_associative_arrays(array $array, array $add_array): array
    {
        if (!count($add_array)) {
            return $array;
        }

        foreach ($add_array as $key => $item) {
            $array[$key] = $item;
        }

        return $array;
    }
}