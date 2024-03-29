<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "Apple".
 *
 * @property int   $Id               Идентификатор записи
 * @property int   $Color            Цвет яблока
 * @property float $IntegrityPercent Процент целостности
 * @property int   $CreatedAt        Время появления
 * @property int   $FalledAt         Время падения
 * @property int   $DeletedAt        Время полного поедания
 */
class Apple extends ActiveRecord
{
    const TIME_TO_BAD_STATE = 3600 * 5;

    const STATE_NAME_BAD     = 'Испортилось';
    const STATE_NAME_EAT     = 'Можно есть';
    const STATE_NAME_TREE    = 'На дереве';
    const STATE_NAME_DELETED = 'Удалено';
    const STATE_NAME_UNKNOWN = 'Неизвестно';

    const STATE_CODE_BAD     = 3;
    const STATE_CODE_EAT     = 1;
    const STATE_CODE_TREE    = 0;
    const STATE_CODE_DELETED = 2;
    const STATE_CODE_UNKNOWN = -1;

    // наименование цветов яблок
    const COLOR_NAME_GREEN  = 'green';
    const COLOR_NAME_RED    = 'red';
    const COLOR_NAME_YELLOW = 'yellow';
    const COLOR_NAME_BAD    = 'black';

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
            $this->Color = rand(0, count(self::COLOR_MAP) - 1);
        }

        $this->IntegrityPercent = 100.00;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Color'], 'integer'],
            [['IntegrityPercent'], 'number', 'min' => 0.00, 'max' => 100.00],
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

        if (!$this->IntegrityPercent) {
            $this->DeletedAt = time();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isBad()
    {
        return (isset($this->FalledAt) && (($this->FalledAt + self::TIME_TO_BAD_STATE) <= time()));
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
        if ($this->isBad()) {
            return static::COLOR_NAME_BAD;
        }

        return self::REVERSE_COLOR_MAP[$this->Color];
    }

    /**
     * @return bool
     */
    public function canEat()
    {
        return !$this->canFall() && !$this->isBad() && !$this->isDeleted();
    }

    /**
     * @return bool
     */
    public function canFall()
    {
        return !isset($this->FalledAt);
    }

    /**
     * @return bool
     */
    public function isDeleted()
    {
        return isset($this->DeletedAt);
    }

    /**
     * @return float|int
     */
    public function getTimeToBad()
    {
        $result = 0;

        if ($this->canEat()) {
            $result = $this->FalledAt + static::TIME_TO_BAD_STATE - time();
            if ($result < 0) {
                $result = 0;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getStateName()
    {
        if ($this->isBad()) {
            return static::STATE_NAME_BAD;
        }

        if ($this->canFall()) {
            return static::STATE_NAME_TREE;
        }

        if ($this->canEat()) {
            return static::STATE_NAME_EAT;
        }

        if ($this->isDeleted()) {
            return static::STATE_NAME_DELETED;
        }

        return static::STATE_NAME_UNKNOWN;
    }

    /**
     * @return int
     */
    public function getState()
    {
        if ($this->isBad()) {
            return static::STATE_CODE_BAD;
        }

        if ($this->canFall()) {
            return static::STATE_CODE_TREE;
        }

        if ($this->canEat()) {
            return static::STATE_CODE_EAT;
        }

        if ($this->isDeleted()) {
            return static::STATE_CODE_DELETED;
        }

        return static::STATE_CODE_UNKNOWN;
    }

    /**
     * {@inheritdoc}
     * @return AppleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return (new AppleQuery(get_called_class()))->active();
    }

}
