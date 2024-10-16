<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\behaviors\TimestampBehavior;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * This is the model class for table "Post".
 * @property int $id
 * @property int $user_id
 * @property string|null $title
 * @property string|null $text
 * @property int|null $post_category_id
 * @property int|null $status
 * @property string|null $image
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property-read PostCategory $postCategory
 */
class Post extends ActiveRecord
{
    public UploadedFile|string|null $imageFile = null;

    public static function tableName(): string
    {
        return 'Post';
    }

    public function rules() // Валидация
    {
        return [
            [['title', 'user_id', 'text', 'post_category_id', 'status'], 'required'],
            ['title', 'string', 'max' => 255],
            ['text', 'string'],
            ['user_id', 'integer'],
            ['post_category_id', 'integer'],
            ['status', 'integer'],

            [['image'], 'string'],
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg, jpeg', 'maxSize' => 1024 * 1024]
        ];
    }

    public function attributeLabels(): array // Перевод
    {
        return [
            'id' => Yii::t('app', 'Идентификатор'),
            'user_id' => Yii::t('app', 'ID пользователя'),
            'title' => Yii::t('app', 'Заголовок'),
            'text' => Yii::t('app', 'Текст'),
            'post_category_id' => Yii::t('app', 'Категория поста'),
            'status' => Yii::t('app', 'Статус'),
            'image' => Yii::t('app', 'Изображение'),
            'created_at' => Yii::t('app', 'Дата создания'),
            'updated_at' => Yii::t('app', 'Дата обновления'),
        ];
    }

    public function fields(): array
    {
        return [
            'id',
            'title',
            'text',
            'post_category' => fn() => $this->postCategory->name,
            'image' => fn() => Yii::$app->request->hostInfo . '/' . $this->image,
            'created_at' => fn() => Yii::$app->formatter->asDatetime($this->created_at)
        ];
    }

    public function behaviors() // Время
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
        ];
    }

    public function getPostCategory(): ActiveQuery
    {
        return $this->hasOne(PostCategory::class, ['id' => 'post_category_id']);
    }

    public function getCategoryName(): string
    {
        return $this->postCategory->name;
    }

    const NEW = 0;
    const PUBLISHED = 10;
    const REJECTED = 20;

    public static function getList(): array
    {
        return [
            self::NEW => 'Новый',
            self::PUBLISHED => 'Опубликован',
            self::REJECTED => 'Отклонен',
        ];
    }

    public function getStatusName(): string
    {
        $statusLabels = self::getList();

        if (isset($statusLabels[$this->status])) {
            return $statusLabels[$this->status];
        } else {
            return Yii::t('app', 'Неизвестный статус');
        }
    }

    public function beforeValidate(): bool // Валидация
    {
        $this->imageFile = UploadedFile::getInstance($this, 'imageFile');
        return parent::beforeValidate();
    }

    public function beforeSave($insert): bool // Загрузка
    {
        if ($this->imageFile) {
            if (!empty($this->image)) {
                $imagePath = Yii::getAlias('@public/') . $this->image;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $randomString = Yii::$app->security->generateRandomString();
            $path = Yii::getAlias('@uploads') . '/' . $randomString . '.' . $this->imageFile->extension;
            if ($this->imageFile->saveAs($path)) {
                $this->image = 'uploads/' . $randomString . '.' . $this->imageFile->extension;
            }
        }
        return parent::beforeSave($insert);
    }
}