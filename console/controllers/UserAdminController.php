<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 09.10.2018
 * Time: 16:21
 */

namespace console\controllers;

use console\models\SignupForm;
use yii\console\Controller;

class UserAdminController extends Controller
{
    public function actionCreate(): void
    {
        $model = new SignupForm();
        $model->username = $this->prompt('Введите имя пользователя:', ['required' => true]);
        $model->email = $this->prompt('Введите имя e-mail:', ['required' => true]);
        $model->password = $this->prompt('Введите пароль:', ['required' => true]);
        if ($model->signup()) {
            $this->stdout('Пользователь ' . $model->username . ' добавлен');
        }
        var_dump($model->errors);
    }
}