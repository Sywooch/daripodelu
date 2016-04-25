<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use backend\models\Catalogue;
use backend\models\Product;
use yii\helpers\ArrayHelper;

/**
 * ProductSearch represents the model behind the search form about `backend\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'group_id', 'status_id', 'pack_amount', 'amount', 'free', 'inwayamount', 'inwayfree', 'user_row'], 'integer'],
            [['code', 'catalogue_id', 'name', 'product_size', 'matherial', 'small_image', 'big_image', 'super_big_image', 'content', 'status_caption', 'brand'], 'safe'],
            [['weight', 'pack_weigh', 'pack_volume', 'pack_sizex', 'pack_sizey', 'pack_sizez', 'enduserprice'], 'number'],
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
    public function search($params, ActiveQuery $query = null)
    {
        $query = is_null($query) ? Product::find() : $query;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 30,
            ],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC]
            ],
        ]);

        $this->load($params);

        $categoryName = trim($this->catalogue_id);
        if ($categoryName != '')
        {
            $categories = Catalogue::find()->where(['like', 'name', $categoryName])->all();
            if (count($categories) > 0)
            {
                $catalogueIds = ArrayHelper::getColumn($categories, 'id');
            }
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'catalogue_id' => $catalogueIds,
            'group_id' => $this->group_id,
            'status_id' => $this->status_id,
            'weight' => $this->weight,
            'pack_amount' => $this->pack_amount,
            'pack_weigh' => $this->pack_weigh,
            'pack_volume' => $this->pack_volume,
            'pack_sizex' => $this->pack_sizex,
            'pack_sizey' => $this->pack_sizey,
            'pack_sizez' => $this->pack_sizez,
            'amount' => $this->amount,
            'free' => $this->free,
            'inwayamount' => $this->inwayamount,
            'inwayfree' => $this->inwayfree,
            'enduserprice' => $this->enduserprice,
            'user_row' => $this->user_row,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'product_size', $this->product_size])
            ->andFilterWhere(['like', 'matherial', $this->matherial])
            ->andFilterWhere(['like', 'small_image', $this->small_image])
            ->andFilterWhere(['like', 'big_image', $this->big_image])
            ->andFilterWhere(['like', 'super_big_image', $this->super_big_image])
            ->andFilterWhere(['like', 'content', $this->content])
            ->andFilterWhere(['like', 'status_caption', $this->status_caption])
            ->andFilterWhere(['like', 'brand', $this->brand]);

        $this->catalogue_id = $categoryName;

        return $dataProvider;
    }
}
