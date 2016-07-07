<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use backend\models\SlaveProduct;

/**
 * SlaveProductSearch represents the model behind the search form about `backend\models\SlaveProduct`.
 */
class SlaveProductSearch extends SlaveProduct
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'amount', 'free', 'inwayamount', 'inwayfree', 'user_row'], 'integer'],
            [['code', 'name', 'size_code', 'price_currency', 'price_name', 'parent_product_id'], 'safe'],
            [['weight', 'price', 'enduserprice'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = SlaveProduct::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'parent_product_id' => SORT_ASC,
                    'id' => SORT_ASC,
                ]
            ],
        ]);

        $this->load($params);

        $parentProductName = trim($this->parent_product_id);
        if ($parentProductName != '') {
            $products = Product::find()->where(['like', 'name', $parentProductName])->all();
            if (count($products) > 0) {
                $parentProductIds = ArrayHelper::getColumn($products, 'id');
            }
        }

        if ( !$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
//            'parent_product_id' => $this->parent_product_id,
            'weight' => $this->weight,
            'price' => $this->price,
            'amount' => $this->amount,
            'free' => $this->free,
            'inwayamount' => $this->inwayamount,
            'inwayfree' => $this->inwayfree,
            'enduserprice' => $this->enduserprice,
        ]);

        $query->andFilterWhere(['parent_product_id' => $parentProductIds]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'size_code', $this->size_code])
            ->andFilterWhere(['like', 'price_currency', $this->price_currency])
            ->andFilterWhere(['like', 'price_name', $this->price_name]);

        $this->parent_product_id = $parentProductName;

        return $dataProvider;
    }
}
