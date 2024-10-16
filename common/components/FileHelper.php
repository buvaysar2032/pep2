<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 21.03.2019
 * Time: 15:58
 */

namespace common\components;


use Exception;
use RuntimeException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class FileHelper
{
    public static function generateFileName(UploadedFile $image, string $file_name): string
    {
        return UserUrlManager::USER_UPLOADS . $file_name . '.' . $image->getExtension();
    }

    //
    public static function generateUserFileName(UploadedFile $image = null, int $user_id = null): string
    {
        $extension = $image ? '.' . $image->getExtension() : '';
        return UserUrlManager::USER_UPLOADS . DIRECTORY_SEPARATOR . time() . '_' . ($user_id
                ?: Yii::$app->user->id) . $extension;
    }

    public static function stringToBytes($value): array|string|null
    {
        return preg_replace_callback('/^\s*(\d+)\s*(?:([kmgt]?)b?)?\s*$/i', function ($m) {
            switch (strtolower($m[2])) {
                case 't':
                    $m[1] *= 1024 ** 4;
                    break;
                case 'g':
                    $m[1] *= 1024 ** 3;
                    break;
                case 'm':
                    $m[1] *= 1024 ** 2;
                    break;
                case 'k':
                    $m[1] *= 1024;
                    break;
            }
            return $m[1];
        }, $value);
    }

    public static function bytesToString(string|int|null $value = null, $to = null): int|string|null
    {
        if (is_null($value)) {
            return $value;
        }
        $l = ['B', 'K', 'M', 'G', 'T'];
        $value = (int)$value;

        if (!$to) {
            foreach ($l as $iValue) {
                if (floor($value / 1024) <= 0) {
                    return round($value, 2) . $iValue;
                }
                $value /= 1024;
            }
            return $value;
        }

        $to = strtoupper($to);
        $index = array_search($to, $l);
        if ($index === false) {
            return $value;
        }

        return (round($value / (1024 ** $index))) . $l[$index];
    }

    /**
     * Сохранение картинки
     * @throws Exception
     */
    public static function saveFile(
        ?UploadedFile $image,
        string|null $file_name,
        array|string|bool $file_type = null,
        int $max_size = null,
        $return_absolute_path = false
    ): array|string|null {
        if (!$image) {
            return ['error' => 'Файл не передан'];
        }

        // Check file type
        if (is_string($file_type)) {
            $file_type = [$file_type];
        } elseif ($file_type === true) {
            $file_type = ['image/jpg', 'image/jpeg', 'image/png'];
        }
        if ($file_type && !in_array($image->type, $file_type, true)) {
            return ['error' => 'Не допустимый формат файла'];
        }

        // Check filez size
        if (!$max_size) {
            $max_size = ArrayHelper::getValue(Yii::$app->params, 'upload-max-size');
        }
        if ($max_size && $image->size > $max_size) {
            return ['error' => 'Размер файла не должен превышать ' . round($max_size / 1024 / 1024, 3) . ' Mb.'];
        }

        // /
        $prefix = explode(DIRECTORY_SEPARATOR, $file_name);
        if (count($prefix) > 1) {
            $file_name = array_pop($prefix);
            $prefix = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $prefix);
        }

        $fileDir = Yii::getAlias('@uploads') . $prefix;
        self::checkDir($fileDir);

//        $image_name = $file_name;// . '.'.$image->getExtension();
        $filePath = $fileDir . DIRECTORY_SEPARATOR . $file_name;

        if (!$image->saveAs($filePath)) {
            return ['error' => $image->errors];
        }
//        return $new_image; // !!!
        return $return_absolute_path ? $file_name : $filePath;
    }


    //
    public static function deleteFile(string $file_name, bool $uploads_folder = true): bool
    {
        if ($uploads_folder) {
            $fileDir = Yii::getAlias('@uploads');
            $file_name = $fileDir . $file_name;
        }
        if (!file_exists($file_name)) {
            return false;
        }
        if (unlink($file_name) !== true) {
            return false;
        }
        return true;
    }


    //проверка директории
    public static function checkDir($path): void
    {
        if (!is_dir($path)) {
            if (!mkdir($path) && !is_dir($path)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $path));
            }
            chmod($path, '0777');
        }
    }

}