<?php

use admin\components\widgets\AdminWidgetHelper;
use kartik\{export\ExportMenu, grid\ActionColumn, grid\GridView, grid\SerialColumn};
use yii\helpers\Html;

/**
 * @var $this         yii\web\View
 * @var $searchModel  common\models\UserSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    $gridColumns = [
        ['class' => SerialColumn::class],
        'id',
        'username',
        'auth_source',

        'userExt.first_name',
        'userExt.middle_name',
        'userExt.last_name',

        'userExt.email',
        'userExt.email_is_verified',
        'userExt.email_verified_at',

        'last_login_at:datetime',
        'created_at:datetime',
    ];

    // Renders a export dropdown menu
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            AdminWidgetHelper::getFixedWidthColumn(),
            'username',
            'auth_source',
            'userExt.last_name',
            [
                'label' => 'Email',
                'value' => static function ($data) {
                    return $data->userExt->email
                        ? '<span style="color:green" title="' . Yii
                            ::t('app', 'Email is confirmed') . '">' . $data->userExt->email . '</span>'
                        : ('<span style="color:red" title="' . Yii
                                ::t(
                                    'app',
                                    'Email is not confirmed'
                                ) . '">' . $data->userExt->unconfirmed_email . '</span>');
                },
                'format' => 'raw',
            ],
            AdminWidgetHelper::getDataRangeItem('last_login_at', $searchModel),
            AdminWidgetHelper::getDataRangeItem('created_at', $searchModel),

            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}'
            ],
        ],
    ]); ?>
</div>
