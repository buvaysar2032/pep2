<?php

namespace admin\controllers;

use common\models\Setting;
use kartik\grid\EditableColumnAction;
use yii\data\ActiveDataProvider;

class SettingController extends AdminController
{

    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Setting::find()
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    public function actions(): array
    {
        return [
            'change' => [
                'class' => EditableColumnAction::class,
                'modelClass' => Setting::class,
            ]
        ];
    }
}
