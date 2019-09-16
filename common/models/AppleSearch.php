<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Apple;

/**
 * AppleSearch represents the model behind the search form of `common\models\Apple`.
 */
class AppleSearch extends Apple
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Color'], 'integer'],
            [['IntegrityPercent'], 'number'],
            [['CreatedAt', 'FalledAt', 'DeletedAt'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     * @return ActiveDataProvider
     */
    public function search()
    {
        $query = Apple::find();

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => false,
        ]);

        return $dataProvider;
    }
}
