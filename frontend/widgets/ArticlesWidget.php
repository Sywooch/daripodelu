<?php

namespace frontend\widgets;

use yii;
use yii\base\Widget;
use frontend\models\Article;

class ArticlesWidget extends Widget
{

    const ON_MAIN_PAGE = 'main';
    const ON_SECOND_PAGE = 'second';
    const MODE_LAST_ARTICLES = 1;

    /**
     * @var int quantity of news which displayed in the block on the page
     */
    private $quantity;

    private $page = self::ON_MAIN_PAGE;

    private $mode = self::MODE_LAST_ARTICLES;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = new Article();
        if ($this->mode == static::MODE_LAST_ARTICLES) {
            $articles = $model->getLastArticles($this->quantity);
        } else {
            throw new \Exception('This mode is not exists');
        }

        return $this->render('main', [
            'articles' => $articles,
        ]);
    }

    public function getViewPath()
    {
        return Yii::getAlias('@frontend/views/widgets/articles');
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        if ( !is_int($quantity)) {
            throw new yii\base\InvalidParamException('The parameter must be an integer value');
        }

        $this->quantity = $quantity;
    }

    /**
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param string $page
     */
    public function setPage($page)
    {
        $this->page = $page == self::ON_MAIN_PAGE ?: self::ON_SECOND_PAGE;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param int $mode
     */
    public function setMode($mode)
    {
        $this->mode = (int)$mode;
    }
}