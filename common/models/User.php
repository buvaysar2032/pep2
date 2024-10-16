<?php

namespace common\models;

use Throwable;
use Yii;
use yii\base\Exception;
use yii\db\{ActiveQuery, ActiveRecord, StaleObjectException};
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int              $id
 *
 * @property string           $username
 *
 * @property string           $password_hash
 * @property string           $password_reset_token
 * @property string           $auth_source
 * @property string           $auth_key
 *
 * @property int              $last_login_at
 * @property int              $created_at
 * @property int              $updated_at
 *
 * @property int              $status
 *
 * @property-read UserExt          $userExt
 * @property-read null|string $authKey
 */
class User extends ActiveRecord implements IdentityInterface
{

    public const SCENARIO_SIGNUP = 1;
    public const SCENARIO_SIGNUP_SOCIAL = 2;
    public const SCENARIO_LOGIN = 3;
    public const SCENARIO_UPDATE = 4;

    public ?string $email = null;
    public ?string $password = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required', 'on' => self::SCENARIO_SIGNUP],
            [['email', 'password'], 'required', 'on' => self::SCENARIO_LOGIN],

            [['email'], 'email'],
            [['password'], 'string', 'min' => 6, 'max' => 20],

            [['last_login_at', 'created_at', 'updated_at', 'status'], 'integer'],
            [['username', 'auth_source', 'auth_key', 'password_reset_token'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_SIGNUP => ['email', 'password'],
            self::SCENARIO_SIGNUP_SOCIAL => ['email', 'password', 'username'],
            self::SCENARIO_LOGIN => ['email', 'password'],
            self::SCENARIO_UPDATE => ['email', 'password', 'username'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_source' => Yii::t('app', 'Auth Source'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'last_login_at' => Yii::t('app', 'Last Login At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    public function getUserExt(): ActiveQuery
    {
        return $this->hasOne(UserExt::class, ['user_id' => 'id']);
    }

    // ==========================================

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey): ?bool
    {
        return $this->getAuthKey() === $authKey;
    }

    //
    public static function findIdentity($id, $with_ext = false): ?self
    {
        /** @var self|null $user */
        if ($with_ext) {
            $user = self::find()->where(['id' => $id])->with('userExt')->one();
        } else {
            $user = self::findOne(['id' => $id]);
        }
        return $user;
    }

    public static function findUserByAccessToken($token): ?self
    {
        return self::findOne(['auth_key' => $token]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null): ?self
    {
        if ($token) {
            /** @var self|null $user */
            $user = self::find()->where(['auth_key' => $token])->one();
            return $user;
        }
        return null;
    }

    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @throws Exception
     */
    public function setPassword($password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws Exception
     */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    //

    /**
     * @throws Exception
     */
    public function generatePasswordResetToken(): void
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    // -----------------------

    public static function findByUsername(?string $username): ?User
    {
        return self::findOne(['username' => $username]);
    }

    //
    public static function getUserByEmail($email): ?self
    {
        if (!$email) {
            return null;
        }
        return UserExt::getByEmail($email, false)?->user;
    }

    public static function getUserByUsername($username): ?self
    {
        if (!$username) {
            return null;
        }

        /** @var self|null $user */
        $user = self::find()->where(['username' => $username])->one();
        return $user;
    }

    public static function getUserByUnconfirmedEmail($email): ?self
    {
        if (!$email) {
            return null;
        }
        return UserExt::getByEmail($email)?->user;
    }

    //
    public static function getById(int|bool $user_id = null): ?self
    {
        if ($user_id === true) {
            $user_id = (int)Yii::$app->user->id;
        }
        /** @var self|null $user */
        $user = self::find()->where(['id' => $user_id])->with('userExt')->one();
        return $user;
    }

    /**
     * Создание нового пользователя
     *
     * @throws Exception
     * @throws StaleObjectException
     * @throws Throwable
     */
    public static function createUser(array $params = null, string|int $scenario = null): User|array
    {
        if (!$params) {
            return ['error' => ['createUser' => Yii::t('app', 'Data required')]];
        }

        $user = new self();
        $user->scenario = $scenario ?: self::SCENARIO_SIGNUP;

        $user->load($params, '');
        if (isset($params['password'])) {
            $user->setPassword($params['password']);
        }
        $user->created_at = $user->updated_at = time();
        $user->auth_key = Yii::$app->security->generateRandomString();
//        self::checkEmailIsChanged($user, $params);
        $user->last_login_at = time();

        if ($user->save()) {
            $user->refresh();

            $userExt = new UserExt();
            $params['user_id'] = $user->id;
            $userExt->load($params, '');

            if (!$userExt->save()) {
                $user->delete();
                return ['error' => ['createUser' => $userExt->errors]];
            }

            return $user;
        }

        return ['error' => ['createUser' => $user->errors]];
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public static function updateData(self $user, array $params): self|array
    {
        $user->scenario = self::SCENARIO_UPDATE;

        $user->load($params, '');
        self::checkEmailIsChanged($user, $params);

        if ($user->save()) {
            $user->refresh();

            $userExt = $user->userExt;
            $userExt->load($params, '');

            if (!$userExt->save()) {
                $user->delete();
                return ['error' => ['updateData' => $userExt->errors]];
            }

            return $user;
        }
        return ['error' => ['updateData' => $user->errors]];
    }

    //
    public static function checkEmailIsChanged(self $user, array &$params): void
    {
        if (isset($params['email'])) {
            $user->auth_source = 'e-mail';
            $params['unconfirmed_email'] = $params['email'];
            unset($params['email']);
        }
    }
}
