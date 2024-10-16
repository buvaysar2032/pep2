<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 14.11.2018
 * Time: 11:32
 */

namespace common\components;

use Exception;
use Yii;
use yii\helpers\{ArrayHelper, Url};
use yii\web\UrlManager;

/**
 * @property-read string $uploadsPath
 */
class UserUrlManager extends UrlManager
{

    public const UPLOADS = '/uploads';
    public const USER_UPLOADS = '/users';

    public string $root = '/htdocs';

    public bool $hideRoot = true;

    public function createUrl($params)
    {
        $url = parent::createUrl($params);
        if ($this->hideRoot) {
            $url = $this->removeRoot($url);
        }
        return $url;
    }

    /**
     * @throws Exception
     */
    public static function getProtocol()
    {
        $params = Yii::$app->params ?? [];

        $protocol = ArrayHelper::getValue($params, '__PROTOCOL__');
        if ($protocol) {
            return $protocol;
        }

        $protocol_param = ArrayHelper::getValue($params, 'fixedProtocol');
        $protocol = ($protocol_param
            ?: ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
                ? 'https' : 'http'));
        ArrayHelper::setValue($params, '__PROTOCOL__', $protocol); // cache
        return $protocol;
    }

    /**
     * @throws Exception
     */
    public static function getDomainUrl($app = null, $prefix = true, $suffix = false)
    {
        // Get the value from the Cache
        if (Yii::$app) {
            $url = ArrayHelper::getValue(Yii::$app->params, '_domain_url_');
        }

        if (!isset($url)) {
            if (!$app) {
                $app = '@app';
            }
            $pathInfo = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

            if (Yii::$app) {
                $root = ArrayHelper::getValue(Yii::$app->params, 'rootDomain');
            }
            $root_pos = isset($root) ? stripos($pathInfo, $root) : false;
            if ($root_pos !== false) {
                $url = substr($pathInfo, 0, $root_pos) . substr($pathInfo, $root_pos + strlen($root));
            } else {
                $url = $pathInfo;
            }


            $app = basename(Yii::getAlias($app)) . '/';

            $pos = stripos($pathInfo, $app);
            $basePath = substr($url, 0, $pos);
            $params_pos = strpos($basePath, '?');
            if ($params_pos) {
                $url = substr($basePath, 0, $params_pos);
            } else {
                $url = $basePath;
            }

            $url = rtrim($url, '/');

            if ($prefix) {
                $protocol = self::getProtocol();

                if (Yii::$app) {
                    $protocol_param = ArrayHelper::getValue(Yii::$app->params, 'fixedProtocol');
                }

                $url = ($protocol_param ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === 443)
                        ? 'https' : 'http')) . '://' . $url;
            }

            if (Yii::$app) {
                Yii::$app->params['_domain_url_'] = $url; // Put the value from the Cache
            }
        }

        if ($suffix) {
            $url .= $suffix;
        }

        return $url;
    }

    //

    /**
     * @throws Exception
     */
    public static function setAbsoluteUploadsPath($data, $fields = null, $prefix = null)
    {
        if (!$data) {
            return $data;
        }

        $prefix = self::getDomainUrl(null, true, $prefix ?: self::UPLOADS);

        // When a string data passed
        if (is_string($data)) {
            return $prefix . $data;
        }

        function _traverse($data, $prefix, $fields)
        {
            foreach ($data as $i => $item) {
                if (is_array($item)) {
                    $data[$i] = _traverse($item, $prefix, $fields);
                } elseif ($item) {
                    foreach ($fields as $field) {
                        if ($i === $field) {
                            $data[$i] = $prefix . $item;
                        }
                    }
                }
            }
            return $data;
        }

        return _traverse($data, $prefix, $fields);
    }


    //
    private function removeRoot($url)
    {
        $root_pos = stripos($url, $this->root);
        //убираем папку root из URL
        if ($root_pos !== false) {
            $url = substr($url, 0, $root_pos) . substr($url, $root_pos + strlen($this->root));
        }
        return $url;
    }

    //
    public function getUploadsPath(): string
    {
        $imgPath = Url::base();
        $app = basename(Yii::getAlias('@app'));
        $pos = stripos($imgPath, $app);
        $imgPath = substr($imgPath, 0, $pos - 1);
        $imgPath .= Yii::getAlias('@images') . '/';
        return $imgPath;
    }
}