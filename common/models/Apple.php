<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

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
    const TIME_TO_BAD_STATE = 3600 * 5;

    // наименование цветов яблок
    const COLOR_NAME_GREEN  = 'green';
    const COLOR_NAME_RED    = 'red';
    const COLOR_NAME_YELLOW = 'yellow';

    // коды цветов для БД
    const COLOR_MAP = [
        self::COLOR_NAME_GREEN  => 0,
        self::COLOR_NAME_RED    => 1,
        self::COLOR_NAME_YELLOW => 2,
    ];

    const REVERSE_COLOR_MAP = [
        self::COLOR_NAME_GREEN,
        self::COLOR_NAME_RED,
        self::COLOR_NAME_YELLOW,
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Apple';
    }

    /**
     * Apple constructor.
     *
     * @param string|null $color
     * @param array       $config
     *
     * @throws Exception
     */
    public function __construct(string $color = null, $config = [])
    {
        parent::__construct($config);

        if (isset($color)) {
            $this->Color = self::COLOR_MAP[$color];
            if (!isset($this->Color)) {
                throw new Exception(Yii::t('app', 'Apple color is bad'), 500);
            }
        } else {
            $this->Color = self::REVERSE_COLOR_MAP[random(0, count(self::COLOR_MAP))];
        }
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

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'      => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['CreatedAt'],
                ],
            ],
        ];
    }

    /**
     * @param float $percent
     *
     * @return $this
     * @throws Exception
     */
    public function eat(float $percent)
    {
        if (isset($this->DeletedAt)) {
            throw new Exception(Yii::t('app', 'Apple alredy eated'), 403);
        }

        if (!isset($this->FalledAt)) {
            throw new Exception(Yii::t('app', 'Apple not falled'), 403);
        }

        if ($this->isBad()) {
            throw new Exception(Yii::t('app', 'Apple already bad'), 403);
        }

        $this->IntegrityPercent = round($this->IntegrityPercent - $this->IntegrityPercent * $percent / 100, 2);

        return $this;
    }

    /**
     * @return bool
     */
    public function isBad()
    {
        return (isset($this->FalledAt) && (($this->FalledAt + self::TIME_TO_BAD_STATE)>=time()));
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function fall()
    {
        if (isset($this->FalledAt)) {
            throw new Exception(Yii::t('app', 'Apple already falled'), 403);
        }

        $this->FalledAt = time();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getColorName()
    {
        return self::REVERSE_COLOR_MAP[$this->Color];
    }
}
