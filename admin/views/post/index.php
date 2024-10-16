<?php

use common\models\Post;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\PostSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Post'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'user_id',
            'title',
            'text:ntext',
            [
                'attribute' => 'post_category_id',
                'label' => Yii::t('app', 'Категория поста'),
                'value' => function (Post $model) {
                    return $model->getCategoryName();
                },
                'filter' => \yii\helpers\ArrayHelper::map(\common\models\PostCategory::find()->all(), 'id', 'name'),
                'contentOptions' => ['style' => 'width: 200px;'],
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('app', 'Статус'),
                'value' => function ($model) {
                    return $model->getStatusName();
                },
                'filter' => Post::getList(),
                'contentOptions' => ['style' => 'width: 200px;'],
            ],
            [
                'attribute' => 'image',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->image ? Html::img('/' . $model->image, ['alt' => 'Изображение поста', 'style' => 'width: auto; height: auto']) : Yii::t('app', 'Нет изображения');
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Post $model) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
            ],
        ],
    ]); ?>

</div>
