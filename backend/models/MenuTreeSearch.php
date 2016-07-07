<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\MenuTree;

/**
 * MenuTreeSearch represents the model behind the search form about `app\models\MenuTree`.
 */
class MenuTreeSearch extends MenuTree
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'lft', 'rgt', 'depth', 'ctg_id', 'item_id', 'can_be_parent', 'show_in_menu', 'show_as_link', 'status'], 'integer'],
            [['name', 'alias', 'module_id', 'controller_id', 'action_id'], 'safe'],
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
        $query = MenuTree::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['lft'],
                'defaultOrder' => ['lft' => SORT_ASC],
            ],
        ]);

        $this->load($params);

        if ( !$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'lft' => $this->lft,
            'rgt' => $this->rgt,
            'depth' => $this->depth,
            'ctg_id' => $this->ctg_id,
            'item_id' => $this->item_id,
            'show_in_menu' => $this->show_in_menu,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'module_id', $this->module_id])
            ->andFilterWhere(['like', 'controller_id', $this->controller_id])
            ->andFilterWhere(['like', 'action_id', $this->action_id]);

        return $dataProvider;
    }
}
