<?php

namespace common\models;

use admin\models\AdminModel;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "user_ext".
 *
 * @property int       $id
 *
 * @property int       $user_id
 *
 * @property string    $first_name
 * @property string    $middle_name
 * @property string    $last_name
 *
 * @property string    $phone
 *
 * @property string    $unconfirmed_email
 * @property string    $email
 * @property string    $email_confirm_token
 * @property integer   $email_is_verified
 * @property integer   $email_verified_at
 *
 * @property integer   $rules_accepted
 *
 * @property-read User $user
 */
class UserExt extends AdminModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_ext}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [

            [['user_id'], 'required'],

            [['user_id'], 'integer'],

            // name
            [['first_name', 'middle_name', 'last_name', 'phone'], 'string', 'min' => 3, 'max' => 255],

            // email
            [['unconfirmed_email', 'email', 'email_confirm_token'], 'string', 'max' => 255],
            [['email_is_verified', 'email_verified_at'], 'integer'],

            //
            [['rules_accepted'], 'integer'],

            //
            [
                ['user_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['user_id' => 'id']
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),

            'user_id' => Yii::t('app', 'User ID'),

            'first_name' => Yii::t('app', 'First Name'),
            'middle_name' => Yii::t('app', 'Middle Name'),
            'last_name' => Yii::t('app', 'Last Name'),

            'phone' => Yii::t('app', 'Phone'),

            'unconfirmed_email' => Yii::t('app', 'Unconfirmed Email'),
            'email' => Yii::t('app', 'Email'),
            'email_is_verified' => Yii::t('app', 'Email is confirmed'),
            'email_verified_at' => Yii::t('app', 'Email Verified At'),

            'rules_accepted' => Yii::t('app', 'Rules Accepted'),
        ];
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    // --------------------------------------

    //
    public static function getByUserId($user_id): ?self
    {
//        if( !$user_id ) return ['error' => ['getByUserId' => Yii::t( 'app', 'User is not found')]];
        if (!$user_id) {
            return null;
        }
        /** @var self|null $userExt */
        $userExt = self::find()->where(['user_id' => $user_id])->one();
        return $userExt;
    }

    //
    public static function getByEmail($email, $use_unconfirmed = true): ?self
    {
        if (!$email) {
            return null;
        }
        /** @var self|null $userExt */
        if ($use_unconfirmed) {
            $userExt = self::find()->where(['email' => $email])->orWhere(['unconfirmed_email' => $email])->one();
        } else {
            $userExt = self::find()->where(['email' => $email])->one();
        }
        return $userExt;
    }


    //
    public static function getByConfirmToken($token): ?self
    {
        /** @var self|null $userExt */
        $userExt = self::find()->where(['email_confirm_token' => $token])->one();
        return $userExt;
    }


    //
    public static function confirmEmail($token): array|true
    {
        $userExt = self::getByConfirmToken($token);

        if (!$userExt) {
            return ['error' => 'token_is_not_valid'];
        }

        if ($userExt->email_is_verified == 1) {
            return ['error' => 'email_is_confirmed'];
        }

        $userExt->email = $userExt->unconfirmed_email;
        $userExt->unconfirmed_email = null;
        $userExt->email_confirm_token = null;
        $userExt->email_is_verified = 1;
        $userExt->email_verified_at = time();
        if (!$userExt->save()) {
            return ['error' => ['email_update_error' => $userExt->errors]];
        }
        return true;
    }


    //
    public static function updateEmail($user_id = null, $email = null): true|array|null
    {
        if (!$user_id || !$email) {
            return null;
        }
        $userExt = self::getByUserId($user_id);
        if (!$userExt) {
            return null;
        }
        $userExt->unconfirmed_email = $email;
        if (!$userExt->save()) {
            return ['error' => ['email_update_error' => $userExt->errors]];
        }
        return true;
    }

}
