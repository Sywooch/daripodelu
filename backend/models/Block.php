<?php

namespace backend\models;

use Yii;
use yii\base\InvalidParamException;

/**
 * This is the model class for table "{{%block}}".
 *
 * @property integer $id
 * @property string $position
 * @property string $name
 * @property string $title
 * @property string $content
 * @property integer $weight
 * @property integer $show_all_pages
 * @property string $show_on_pages
 */
class Block extends \yii\db\ActiveRecord
{
    const NO_POS = 'no_pos';

    /**
     * @var array
     */
    private $positions = [Block::NO_POS => 'Нет позиции'];
    /**
     * @var \common\models\MenuTree
     */
    private $menuTree = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%block}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'name', 'weight'], 'required'],
            [['content', 'show_on_pages'], 'string'],
            [['weight', 'show_all_pages'], 'integer'],
            [['position'], 'string', 'max' => 40],
            [['name'], 'string', 'max' => 100],
            [['title'], 'string', 'max' => 255],
            [['position', 'name', 'title', 'content', 'weight', 'show_on_pages'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'position' => Yii::t('app', 'Позиция на странице'),
            'name' => Yii::t('app', 'Название'),
            'title' => Yii::t('app', 'Заголовок'),
            'content' => Yii::t('app', 'Текст'),
            'weight' => Yii::t('app', 'Порядок следования'),
            'show_all_pages' => Yii::t('app', 'Показывать на всех страницах'),
            'show_on_pages' => Yii::t('app', 'Показывать только на страницах'),
        ];
    }

    /**
     * Returns the name of the position $position
     * @param $position string name of the position
     * @return mixed
     * @throws \Exception
     */
    public function positionName($position)
    {
        if ( !isset($this->position[$position]))
        {
            throw new \Exception('Position "' . $position . '" is not exist');
        }

        return $this->positions[$position];
    }

    /**
     * Returns the list of the positions
     * @return array
     */
    public function positions()
    {
        return $this->positions;
    }

    /**
     * Adds a list of new positions
     * @param $positions array of pairs $positionCode => $poasiotionName
     */
    public function addPositions($positions)
    {
        if ( !is_array($positions))
        {
            throw new InvalidParamException('Invalid parameter passed to addPosition method. Parameter $positions must be an array.');
        }

        foreach ($positions as $key => $value)
        {
            $this->addPosition($key, $value);
        }
    }

    /**
     * Adds a new position
     * @param $code string a uniqe characters code of position
     * @param $name string a name of position
     * @throws \Exception
     */
    public function addPosition($code, $name)
    {
        if ($code == Block::NO_POS)
        {
            throw new \Exception('Position named "' . $code . '" can not be added. Name "' . $code . '" is reserved.');
        }

        $this->positions[$code] = $name;
    }

    /**
     * Removes the list of positions
     * @param $positions array list of characters code of positions
     */
    public function removePositions($positions)
    {
        if ( !is_array($positions))
        {
            throw new InvalidParamException('Invalid parameter passed to removePositions method. Parameter $positions must be an array.');
        }

        foreach ($positions as $position)
        {
            $this->addPosition($position);
        }
    }

    /**
     * Removes position
     * @param $position characters code of position
     */
    public function removePosition($position)
    {
        if (isset($this->positions[$position]) && $position != Block::NO_POS)
        {
            unset($this->positions[$position]);
        }
    }

    /**
     * Attaches MenuTree object
     * @param \common\models\MenuTree $menuTree
     */
    public function attachMenuTree(\common\models\MenuTree $menuTree)
    {
        $this->menuTree = $menuTree;
    }

    /**
     * Returns the list of pages relative URL
     *
     * For example,
     *      [
     *          ['/' => '/'],
     *          ['articles/detective-novels/novel.html' => 'articles/detective-novels/novel.html'],
     *          ['news.html' => 'news.html'],
     *      ]
     * @return array
     */
    public function getPagesList()
    {
        $pagesList = [];
        if ( !is_null($this->menuTree))
        {
            $pagesList[] = '/';
            foreach ($this->menuTree->getRoutes() as $alias => $paramsList)
            {
                $pagesList[$alias . yii::$app->urlManager->suffix] = $alias . yii::$app->urlManager->suffix;
            }
        }

        return $pagesList;
    }

    /**
     * Returns the max value of the "weight" column for specified position
     * @param $position string
     * @return int
     */
    public function getMaxWeightForPosition($position)
    {
        return Block::find()->where(['position' => $position])->max('[[weight]]');
    }
}
