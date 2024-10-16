<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 11.03.2019
 * Time: 15:59
 */

namespace common\components;


use Exception;
use Yii;
use yii\helpers\ArrayHelper;

class Dictionary
{
    /**
     * Получение списка
     * @throws Exception
     */
    public static function getList(string $list_key, string $attr = null, bool $html = false)
    {
        $dictionary = Yii::$app->params['dictionary'];
        if (!$dictionary) {
            return false;
        }

        // Caching
        $cached_name = '_cached_lists.' . $list_key . '.' . $attr . '.' . $html;
        $cached = ArrayHelper::getValue($dictionary, $cached_name);
        if ($cached) {
            return $cached;
        }

        $list = $dictionary[$list_key];
        if (!$list) {
            return false;
        }

        if ($attr) {
            if ($attr === true) {
                $attr = 'description';
            }
            $list_ = [];
            foreach ($list as $key => $list_item) {
                $val = $list_item[$attr];
                if ($html) {
                    $val = self::_wrap($val, ArrayHelper::getValue($list_item, 'color'));
                }
                $list_[$key] = $val;
            }
            $list = $list_;
        }

        ArrayHelper::setValue($dictionary, $cached_name, $list);
        return $list;
    }

    // Получение определенного ключа

    /**
     * @throws Exception
     */
    public static function getKey(string $list_key, string $attr)
    {
        $dictionary = Yii::$app->params['dictionary'];
        if (!$dictionary) {
            return false;
        }

        // Caching
        $cached_name = '_cached_keys.' . $list_key . '.' . $attr;
        $cached = ArrayHelper::getValue($dictionary, $cached_name);
        if ($cached) {
            return $cached;
        }

        $list = $dictionary[$list_key];

        foreach ($list as $key => $list_item) {
            if ($list_item['name'] === $attr) {
                ArrayHelper::setValue($dictionary, $cached_name, $key);
                return $key;
            }
        }

        return false;
    }

    // Получение определенного ключа

    /**
     * @throws Exception
     */
    public static function getValue(string $list_key, string|int $item_key, string|true $attr = null, bool $html = false)
    {
        $dictionary = Yii::$app->params['dictionary'];
        if (!$dictionary) {
            return false;
        }

        $list = $dictionary[$list_key];

        if ($attr === true) {
            $attr = 'description';
        }

        $item = $list[$item_key];
        if (!$item) {
            return false;
        }

        $value = $attr ? $item[$attr] : $item;
        if ($html) {
            return self::_wrap($value, ArrayHelper::getValue($item, 'color'));
        }

        return $value;
    }


    private static function _wrap(string $txt, string $color = ''): string
    {
        return "<span style='color:$color;font-weight:bold;'>$txt</span>";
    }

}