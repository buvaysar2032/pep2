<?php

use admin\components\widgets\AdminWidgetHelper;
use kartik\grid\{ActionColumn, GridView, SerialColumn};
use yii\helpers\Html;

/**
 * @var $this         yii\web\View
 * @var $searchModel  common\models\TextSearch
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = Yii::t('app', 'Texts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create Text'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => SerialColumn::class],

            AdminWidgetHelper::getFixedWidthColumn(),
            AdminWidgetHelper::getEditableItem('key'),
            AdminWidgetHelper::getEditableItem('value'),

            ['class' => ActionColumn::class],
        ],
    ]); ?>
</div>
