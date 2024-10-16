<?php
/**
 * Created by PhpStorm.
 * User: d.korablev
 * Date: 28.11.2018
 * Time: 14:53
 */

namespace admin\components\parsers;

use Yii;
use yii\db\Exception;


class CSVParser
{
    public $tempDir;

    public function getData($model, $field)
    {
        if (!is_dir($this->tempDir)) {
            mkdir($this->tempDir);
        }
        $model->path = $this->tempDir . $model->$field->baseName . '.' . $model->$field->extension;
        $model->$field->saveAs($model->path);
        return $this->csvToArray($model->path);
    }

    public function csvToArray($path): array
    {
        $handle = fopen($path, 'rb+');
        while (($stringArray = fgetcsv($handle, 0, ',', '"')) !== false) {
            $values[] = $stringArray;
        }
        return $values;
    }


    public function arrayToCsv($data, $handle): void
    {
        fputcsv($handle, $data, ',', '"');
    }

    public function getFloatFromString($number): float
    {
        $number = explode(',', $number);
        return (int)$number[0] + 0.1 * $number[1];
    }

    /**
     * @throws Exception
     */
    public function insertCsvToDb($name, $tableName, array|string $fields): void
    {
        $inFile = str_replace('\\', '/', $name);
        $lineEnd = '\n';
        $fields = (array)$fields;
        foreach ($fields as &$field) {
            $field = '`' . $field . '`';
        }
        unset($field);
        $fields = implode(', ', $fields);
        $sql = "
              LOAD DATA LOCAL 
              INFILE '" . $inFile . "' 
              INTO TABLE `" . $tableName . "`
              FIELDS TERMINATED BY ',' 
              ENCLOSED BY '\"' 
              LINES TERMINATED BY '" . $lineEnd . "'
              IGNORE 0 LINES
              (" . $fields . ") 
        ";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function deleteCodesFile($model): void
    {
        unlink($model->path);
    }
}