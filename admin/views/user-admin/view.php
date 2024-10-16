<?php

use admin\components\arrayViewHelper\ArrayViewHelper;
use yii\bootstrap5\Html;
use yii\widgets\DetailView;

/**
 * @var $this  yii\web\View
 * @var $model admin\models\UserAdmin
 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Admins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-admin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'value' => static function ($data) {
                    return ArrayViewHelper::returnValueArray('user-admin', 'status', $data->status);
                },
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
        ],
    ]) ?>

</div>
