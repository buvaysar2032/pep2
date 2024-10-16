<?php

use admin\components\arrayViewHelper\ArrayViewHelper;
use kartik\grid\{ActionColumn, GridView, SerialColumn};
use yii\bootstrap5\Html;

/**
 * @var $this         yii\web\View
 * @var $searchModel  admin\models\UserAdminSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'User Admins');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => static function ($data) {
                    return ArrayViewHelper::returnValueArray('user-admin', 'status', $data->status);
                },
                'filter' => ArrayViewHelper::returnFilterArray('user-admin', 'status'),
            ],
            [
                'attribute' => 'created_at',
                'value' => static function ($data) {
                    return date('Y-m-d H:i:s', $data->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => static function ($data) {
                    return date('Y-m-d H:i:s', $data->updated_at);
                }
            ],

            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>
</div>
