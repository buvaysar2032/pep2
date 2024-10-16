<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 31.07.2018
 * Time: 15:35
 */

namespace common\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

class DateHelper
{
    public const DATE_FORMAT = 'php:Y-m-d';
    public const DATETIME_FORMAT = 'php:Y-m-d H:i:s';
    public const TIME_FORMAT = 'php:H:i:s';

    //

    /**
     * @throws InvalidConfigException
     */
    public static function convert($dateStr, $type = 'date', $format = null): ?string
    {
        if ($type === 'datetime') {
            $fmt = $format ?? self::DATETIME_FORMAT;
        } elseif ($type === 'time') {
            $fmt = $format ?? self::TIME_FORMAT;
        } else {
            $fmt = $format ?? self::DATE_FORMAT;
        }
        return Yii::$app->formatter->asDate($dateStr, $fmt);
    }

    //

    /**
     * @throws InvalidConfigException
     */
    public static function getCurrentTimeStamp($timestamp = false): int|string|null
    {
        if ($timestamp) {
            return time();
        }
        return self::convert(time(), 'datetime');
    }

    /**
     * @throws InvalidConfigException
     */
    public static function getCurrentDate(): ?string
    {
        return self::convert(time());
    }

    //
    public static function convertViewToDB(string $str, string|true $delimeter = null): false|int|string
    {
//        return date("Y/d/m", $str );
        if (empty($str)) {
            return $str;
        }
        if ($delimeter === null) {
            $delimeter = '/';
        }

        // timestamp
        if ($delimeter === true) {
            return strtotime($str);
        }

        $a = explode($delimeter, trim($str));
        return $a[2] . '-' . $a[1] . '-' . $a[0];
    }

    public static function convertDBToView(string $str, string|true $delimeter = null): string
    {
//        return date("Y/d/m", $str );
        if (empty($str)) {
            return $str;
        }
        if ($delimeter === null) {
            $delimeter = '/';
        }

        // timestamp
        if ($delimeter === true) {
            return date("d/m/Y", $str);
        }

        // simple date
        $a = explode('-', trim($str));
        return $a[2] . $delimeter . $a[1] . $delimeter . $a[0];
    }

    public static function convertViewToTimestamp(string $str, string|int $timeadd = null): false|int|string
    {
        if (empty($str)) {
            return $str;
        }

        $str = static::convertViewToDB($str) . $timeadd;
        return strtotime($str);
    }

    public static function convertModelDatesToDB(array $dates, ActiveRecord $model, string|bool $delimeter = null): void
    {
        foreach ($dates as $date) {
            $model->$date = self::convertViewToDB($model->$date, $delimeter, $delimeter);
        }
    }

    public static function convertModelDatesToView(array $dates, ActiveRecord $model, string|bool $delimeter = null): void
    {
        foreach ($dates as $date) {
            $model->$date = self::convertDBToView($model->$date, $delimeter);
        }
    }
}