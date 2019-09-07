<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Apple".
 *
 * @property int    $Id               Идентификатор записи
 * @property int    $Color            Цвет яблока
 * @property string $IntegrityPercent Процент целостности
 * @property string $CreatedAt        Время появления
 * @property string $FalledAt         Время падения
 * @property string $DeletedAt        Время полного поедания
 */
class Apple extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Color'], 'integer'],
            [['IntegrityPercent'], 'number'],
            [['CreatedAt', 'FalledAt', 'DeletedAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id'               => Yii::t('app', 'Идентификатор записи'),
            'Color'            => Yii::t('app', 'Цвет яблока'),
            'IntegrityPercent' => Yii::t('app', 'Процент целостности'),
            'CreatedAt'        => Yii::t('app', 'Время появления'),
            'FalledAt'         => Yii::t('app', 'Время падения'),
            'DeletedAt'        => Yii::t('app', 'Время полного поедания'),
        ];
    }
}
