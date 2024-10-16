<?php

namespace api\modules\v1\controllers;

use common\components\UserUrlManager;
use common\models\{User, UserExt};
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;
use yii\web\Response;

class UserController extends AppController
{

    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authentificator' => [
                'class' => HttpBearerAuth::class,
                'except' => ['login', 'signup', 'password-restore', 'reset-password', 'email-confirm']
            ],
        ]);
    }

    // SIGNUP
    public function actionSignup(): ?array
    {
        $enabled_clients = ArrayHelper::getValue(Yii::$app->params, 'signup.enabled_clients');

        if ($enabled_clients['email-password']) {
            return $this->emailSignup();
        }
        return $this->returnError(Yii::t('app', 'Registration disabled'));
    }

    // LOGIN
    public function actionLogin(): array
    {
        return $this->emailLogin();
    }

    // UPDATE
    public function actionUpdate(): ?array
    {
        $post = Yii::$app->request->post();
        if (!$post) {
            return $this->returnError(Yii::t('app', 'Data required'));
        }

        $user = User::findIdentity(Yii::$app->user->id);
        if (!$user) {
            return $this->returnError(Yii::t('app', 'User is not found'));
        }

        $user_update_result = User::updateData($user, $post);
        if (isset($user_update_result['error'])) {
            return $this->returnError($user_update_result['error']);
        }

        return $this->getProfile($user);
    }


    //Выход из системы
    public function actionLogout(): ?array
    {
        /** @var User $user */
        $user = User::findIdentity(Yii::$app->user->id);

        $user->auth_key = null;
        $user->updated_at = time();

        if ($user->save(false)) {
            Yii::$app->user->logout();
            return $this->returnSuccess(Yii::t('app', 'You have successfully logged out.'));
        }

        return $this->returnError($user->errors);
    }


    //Получение профиля
    public function actionProfile(): ?array
    {
        $user = User::findIdentity(Yii::$app->user->id, true);
//        return ['user' => \Yii::$app->user->id];
//        $user = User::find()->where(['id'=>\Yii::$app->user->id])->with('email')->one();
//        return $user;
        return $this->getProfile($user);
    }


    //Подтверждение почты
    public function actionEmailConfirm($token): Response
    {
        $redirect_url = UserUrlManager::getDomainUrl() . '?confirm_status=';
        $result = UserExt::confirmEmail($token);
        if (isset($result['error'])) {
            return $this->redirect($redirect_url . $result['error']);
        }
        return $this->redirect($redirect_url . 'success');
    }


    //Авторизация по E-mail
    public function emailLogin(): array
    {
        $email = $this->getParameterFromRequest('email');
        $password = $this->getParameterFromRequest('password');

        if (!$email || !$password) {
            return $this->returnError(['email' => 'Wrong Email or Password']);
        }

        $user = User::getUserByEmail($email);

        if (!$user) {
            return $this->returnError(['email' => 'Wrong Email or Password']);
        }
        if (!$user->validatePassword($password)) {
            return $this->returnError(['email' => 'Wrong Email or Password']);
        }

        $user->auth_source = 'e-mail';
        $user->last_login_at = time();

        return $this->getProfile($user);
    }


    //Регистрация пользователя по E-Mail
    public function emailSignup()
    {
        $params = Yii::$app->params;
        $email = $this->getParameterFromRequest('email');
        $password = $this->getParameterFromRequest('password');
        $username = $this->getParameterFromRequest('username');
        $rules_accepted = $this->getParameterFromRequest('rules_accepted');

//        return $this->returnError(['rules_accepted' => ArrayHelper::getValue( $params, 'signup.require.rules_accepted') ]); // !!!

        if (ArrayHelper::getValue($params, 'signup.require.rules_accepted') === true && !$rules_accepted) {
            return $this->returnError(['rules_accepted' => Yii::t('app', 'Must agree to the rules')]);
        }

//        return $this->returnError(['user' => User::getUserByEmail($email) ]); // !!!
//        return $this->returnError(['user' => UserExt::getByEmail($email) ]); // !!!

        if ($email && ArrayHelper::getValue($params, 'signup.unique.email') === true && UserExt::getByEmail($email)) {
            return $this->returnError(['rules_accepted' => Yii::t('app', 'Such Email is already registered')]);
        }
//        if (Email::find()->where(['value' => $email])->one())
//            return $this->returnError(['email' => 'Такой E-mail уже зарегистрирован.']);

        $user = User::createUser(compact('email', 'password', 'username', 'rules_accepted'));
        if (isset($user['error'])) {
            return $user['error'];
        }

        // USER CREATED SUCCESSFULLY!
        // >>> APP ACITIONS >>>

        // <<< APP ACITIONS <<<

        return $this->getProfile($user);
    }

    //Получение профиля пользователя
    public function getProfile($user = null): ?array
    {
        if ($user === null) {
            return null;
        }

        if ($user->auth_key == null) {
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->updated_at = time();
            $user->save(false);
        }

        $profile = [

            'id' => $user->id,
            'access_token' => $user->auth_key,

            'username' => $user->username,

            'first_name' => $user->userExt['first_name'],
            'middle_name' => $user->userExt['middle_name'],
            'last_name' => $user->userExt['last_name'],
            'phone' => $user->userExt['phone'],

            'email' => $user->userExt['email'],
//            'unconfirmed_email' => $user->userExt['unconfirmed_email'],
//            'userExt' => $user->userExt,

        ];

        return $this->returnSuccess($profile, 'profile');
    }
}
