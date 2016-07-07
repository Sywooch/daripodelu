<?php

namespace backend\widgets\grid;

use yii;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Json;
use backend\widgets\grid\TreeGridAssets;


class TreeGrid extends GridView
{

    public $pluginOptions = [];

    public function run()
    {
        $id = $this->tableOptions['id'];
        $view = $this->getView();
        TreeGridAssets::register($view);
        $options = '';
        if (is_array($this->pluginOptions)) {
            if ( !empty($this->pluginOptions)) {
                $options = Json::encode($this->pluginOptions);
            }
        } else {
            throw new \InvalidArgumentException('Attribute "pluginOptions" must be an array.');
        }

        $view->registerJs("$('#$id').treegrid($options);", yii\web\View::POS_READY);

        parent::run();
    }

    /**
     * Renders a table row with the given data model and key.
     * @param mixed $model the data model to be rendered
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $nodeId = $model->id;
        $parentNodeId = $model->parent_id;

        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string)$key;

        $class = 'treegrid-' . $nodeId;

        if ($parentNodeId) {
            $class .= ' treegrid-parent-' . $parentNodeId;
        }
//        $class .= ' treegrid-expanded';
        $options['class'] = $class;

        return Html::tag('tr', implode('', $cells), $options);
    }
}