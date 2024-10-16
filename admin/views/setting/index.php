<?php

use kartik\grid\{EditableColumn, GridView, SerialColumn};
use yii\helpers\Html;

/**
 * @var $this         yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="settings-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => SerialColumn::class],

            'parameter',
            [
                'class' => EditableColumn::class,
                'attribute' => 'value',
                'editableOptions' => ['formOptions' => ['action' => 'change']],
            ],
            [
                'class' => EditableColumn::class,
                'attribute' => 'description',
                'editableOptions' => ['formOptions' => ['action' => 'change']],
            ]
        ]
    ]) ?>
</div>
