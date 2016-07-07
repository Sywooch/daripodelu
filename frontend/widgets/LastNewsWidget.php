<?php

namespace frontend\widgets;

use yii;
use yii\base\Widget;
use frontend\models\News;

class LastNewsWidget extends Widget
{

    const ON_MAIN_PAGE = 'main';
    const ON_SECOND_PAGE = 'second';

    /**
     * @var int quantity of news which displayed in the block on the page
     */
    private $quantity;

    private $page = self::ON_MAIN_PAGE;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $model = new News();
        $news = $model->getLastNews($this->quantity);

        return $this->render('main', [
            'newsList' => $news,
        ]);
    }

    public function getViewPath()
    {
        return Yii::getAlias('@frontend/views/widgets/lastNews');
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
}