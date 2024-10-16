<?php
/**
 * Created by PhpStorm.
 * User: dpotekhin
 * Date: 02.03.2019
 * Time: 15:18
 */

namespace admin\components\widgets;

use common\components\{Dictionary, UserUrlManager};
use Exception;
use kartik\daterange\DateRangePicker;
use kartik\editable\Editable;
use kartik\grid\EditableColumn;
use Throwable;
use yii\helpers\Html;

class AdminWidgetHelper
{
    public static function getFixedWidthColumn($attr = 'id', $width = '20px'): array
    {
        return [
            'attribute' => $attr,
            'options' => ['style' => 'color:red; width:' . $width . '; white-space: no-wrap;'],
        ];
    }

    public static function getLinkToItem($attr, $path): array
    {
        return [
            'attribute' => $attr,
            'value' => function ($data) use ($attr, $path) {
                return Html::a(
                    $data->$attr,
                    ['/' . $path . '/view', 'id' => $data->$attr],
                    ['style' => 'font-weight:bold;', 'target' => '_blank']
                );
            },
            'format' => 'raw',
        ];
    }

    /**
     * @throws Exception
     */
    public static function getDictionaryItem(string $attr, array|string $items): array
    {
        if (is_string($items)) {
            $filter_items = Dictionary::getList($items, true);
            $items = (array)Dictionary::getList($items, true, true);
        } else {
            $filter_items = $items;
        }
        return [
            'attribute' => $attr,
            'value' => function ($model) use ($attr, $items) {
                return $items[$model->$attr];
            },
            'format' => 'raw',
            'filter' => $filter_items,
        ];
    }

    /**
     * @throws Exception
     */
    public static function getDropdownColumn(string $attr, array|string $items, bool $editable = true): array
    {
        if (is_string($items)) {
            $items = (array)Dictionary::getList($items, true);
        }
        if ($editable) {
            return [
                'class' => EditableColumn::class,
                'attribute' => $attr,
                'editableOptions' => function ($model) use ($attr, $items) {
                    return [
                        'inputType' => Editable::INPUT_SELECT2,
                        'inputOptions' => ['data' => $items],
                        'value' => $model->$attr,
                        'formOptions' => ['action' => 'change']
                    ];
                },
                'value' => function ($data) use ($attr, $items) {
                    return $items[$data->$attr];
                },
                'filter' => $items,
            ];
        }

        return [
            'attribute' => $attr,
            'value' => function ($data) use ($attr, $items) {
                return $items[$data->$attr];
            },
            'filter' => $items,
        ];
    }

    public static function getImageItem(string $attr): array
    {
        return [
            'attribute' => $attr,
            'value' => function ($data) use ($attr) {
                return $data->$attr ? "<img src=" . UserUrlManager::setAbsoluteUploadsPath(
                        $data->$attr
                    ) . " style='max-width:50px;max-height:50px;'>" : '<span style="color:gray;">не задано</span>';
            },
            'format' => 'html'
        ];
    }

    //
    public static function getEditableItem(string $attr, string|int $width = null): array
    {
        if ($width && !is_string($width)) {
            $width .= 'px';
        }
        return [
            'class' => EditableColumn::class,
            'attribute' => $attr,
            'options' => [
                'style' => $width
                    ? 'width:' . $width . '; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;'
                    : ''
            ],
            'editableOptions' => ['formOptions' => ['action' => 'change']],
        ];
    }

    /**
     * @throws Throwable
     */
    public static function getDataRangeItem($attr, $searchModel): array
    {
        return [
            'attribute' => $attr,
            'value' => $attr,
            'format' => 'datetime',
            'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => $attr,
                    'pluginOptions' => ['timePicker' => true,],
                ]) . HTML::error($searchModel, $attr)
        ];
    }

}