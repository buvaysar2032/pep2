<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.09.2018
 * Time: 17:17
 */

namespace admin\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\{BadRequestHttpException, Controller, Response};

/**
 *
 * @property-read string $imgPath
 */
class AdminController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'controllers' => ['*'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function getImgPath(): string
    {
        $imgPath = Yii::$app->getHomeUrl();
        $pos = stripos($imgPath, '/admin');
        $imgPath = substr($imgPath, 0, $pos);
        $imgPath .= Yii::getAlias('@images') . '/';
        return $imgPath;
    }

    public function imgNameCheck($image, $attributes = []): ?Response
    {
        if ($image->load(Yii::$app->request->post())) {
            foreach ($attributes as $attribute) {
                $pos = strrchr($image->$attribute, '/');
                if ($pos) {
                    $image->$attribute = substr($pos, 1);
                }
            }
            if ($image->save()) {
                return $this->redirect(['view', 'id' => $image->id]);
            }
        }
        return null;
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): Response|bool
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('site/login');
        }
        if (!parent::beforeAction($action)) {
            return false;
        }
        return true;
    }
}