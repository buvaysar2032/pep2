<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 19.06.2018
 * Time: 15:50
 */

namespace common\components;

use Yii;


class Utils
{
//    const UPLOAD_SUFFIX = '/uploads/';

    public static string $ROOT_DOMAIN = '';
    public static string $base_url = '';
    public static string $upload_folder = '';
    public static string $upload_folder_url = '';

    //
    public static function getBaseUrl(): array|string
    {
        if (self::$base_url === '') {
            self::$base_url = str_replace(self::$ROOT_DOMAIN, '', (new Request())->getBaseUrl());
        }
        return self::$base_url;
    }

    //
    public static function getUploadFolder(): string
    {
        if (self::$upload_folder === '') {
            self::$upload_folder = Yii::getAlias('@root') . Yii::$app->params['uploadFolder'] . '/';
        }
//        return Yii::getAlias('@backend') . '/web' . Yii::$app->params['uploadFolder'];
        return self::$upload_folder;
    }

    public static function deleteUploadedFile($url): bool
    {
        $path = self::getUploadFolder() . $url;
        if (!file_exists($path)) {
            return false;
        }
        unlink($path);
        return true;
    }

    public static function getUploadFolderUrl($url = ''): string
    {
        if (self::$upload_folder_url === '') {
            $domain = self::getDomain() . dirname(self::getBaseUrl());
            self::$upload_folder_url = "http://{$domain}" . Yii::$app->params['uploadFolder'];
        }
        return self::$upload_folder_url . $url;
    }

    public static function getSiteUrl($remove_back_folders = 0): string
    {
        $url = self::getFromRootUrl('', true) . self::getBaseUrl();
        $url_ = explode('/htdocs/', $url);
        return $url_[0];
    }

    //
    public static function getDomain()
    {
        return Yii::$app->request->serverName;
    }

    /**
     * Gets the absolute url from the root if where's no need to use the current app subfolder in the url
     */
    public static function getFromRootUrl(string $params = '', bool $nolastslash = false): string
    {
        $request = Yii::$app->request;
        $url = ($request->isSecureConnection ? 'https://' : 'http://') . $request->serverName;
        $controller = ltrim(Yii::$app->urlManager->createUrl($params), '/');
        $controller = substr($controller, strpos($controller, '/'));
        if ($nolastslash) {
            $controller = rtrim($controller, '/');
        }
        return $url . $controller;
    }


    // ================= STRING ====================================
    public static function camelize($input, $separator = '-'): array|string
    {
        return str_replace($separator, '', ucwords($input, $separator));
    }

    // ================= ARRAY ====================================
    public static function convertArrayToAssosiated($arr): array
    {
        $new_arr = [];
        foreach ($arr as $val) {
            $new_arr[$val] = '';
        }
        return $new_arr;
    }

    public static function getUserIp()
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
        if ($ip === null) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } elseif (strpos($ip, ',')) {
            $ip = substr($ip, 0, strpos($ip, ','));
        }
        return $ip;
    }

}