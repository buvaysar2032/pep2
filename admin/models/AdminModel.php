<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 21.09.2018
 * Time: 17:18
 */

namespace admin\models;


use Yii;
use yii\db\ActiveRecord;
use yii\helpers\HtmlPurifier;

class AdminModel extends ActiveRecord
{
    public function afterFind(): void
    {
        parent::afterFind();
        $actionId = Yii::$app->controller->action->id;
        if ($actionId === 'index' || $actionId === 'view') {
            foreach ($this->attributes as $key => $attribute) {
                $this->$key = $this->prepareText($this->$key);
            }
        }
    }

    public function prepareText(mixed $string, bool $up_first_string = false, bool $html = false): mixed
    {
        if (!is_string($string)) {
            return $string;
        }
        $string = preg_replace("/[\r\n]+/", "\n", $string);
        $string = preg_replace("/[ \t]+/", " ", $string);

        if ($html === false) {
            $string = strip_tags($string);
            $string = htmlspecialchars($string);
        } else {
            $string = str_replace("\n", '<br />', $string);
            $string = HtmlPurifier::process($string, [
                'HTML.Allowed' => 'p,br',
            ]);
        }
        if ($up_first_string) {
            $string = ucfirst($string);
        }
        return $string;
    }
}