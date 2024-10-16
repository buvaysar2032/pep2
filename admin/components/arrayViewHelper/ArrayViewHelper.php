<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 26.02.2019
 * Time: 13:35
 */

namespace admin\components\arrayViewHelper;

class ArrayViewHelper
{

    /**
     * Возвращает значение аттрибута по значению из БД. Используется в виде
     *  'value' => function($data){
     *      return ArrayViewHelper::returnValueArray('user-admin', 'status', $data->status);
     *  }
     *  в GridView или DetailView
     */

    public static function returnValueArray(string $model, string $attribute, string $data): string
    {
        $initArray = self::_getInitArray($model, $attribute);
        return $initArray[$data];
    }

    /**
     * Возвращает массив значений аттрибутов, где ключи - значения из БД. Используется в виде
     * 'filter' => ArrayViewHelper::returnFilterArray('user-admin', 'status')
     * в GridView
     */
    public static function returnFilterArray(string $model, string $attribute): array
    {
        return self::_getInitArray($model, $attribute);
    }

    /**
     * Возвращает массив значений аттрибутов, где ключи - значения из БД.
     */
    private static function _getInitArray(string $model, string $attribute): array
    {
        $_initArray = require __DIR__ . '/array-config.php';
        return $_initArray[$model][$attribute];
    }
}