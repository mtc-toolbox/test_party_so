<?php

namespace common\models;

use yii\db\Expression;
use yii\db\ActiveQuery;

/**
 * Class AppleQuery
 * @package common\models
 */
class AppleQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        return $this->andFilterWhere(['is', self::getPrimaryTableName() . '.DeletedAt', new Expression('null')]);
    }

    /**
     * @return $this
     */
    public function deleted()
    {
        return $this->andFilterWhere(['not', self::getPrimaryTableName() . '.DeletedAt', new Expression('null')]);
    }

    /**
     * @return $this
     */
    public function falled()
    {
        return $this->andFilterWhere(['not', self::getPrimaryTableName() . '.FalledAt', new Expression('null')]);
    }

    /**
     * @return $this
     */
    public function handing()
    {
        return $this->andFilterWhere(['is', self::getPrimaryTableName() . '.FalledAt', new Expression('null')]);
    }

    /**
     * @param null $db
     *
     * @return array|Apple[]
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Apple|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
