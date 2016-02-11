<?php

namespace frontend\widgets;

use yii;
use yii\base\Widget;
use backend\models\Block;


class BlockWidget extends Widget
{
    /**
     * @var array|null
     */
    private static $blocks = null;
    private $position;
    /**
     * The view to be rendered can be specified in one of the following formats:
     *
     * - path alias (e.g. "@app/views/site/index");
     * - absolute path within application (e.g. "//site/index"): the view name starts with double slashes.
     *   The actual view file will be looked for under the [[Application::viewPath|view path]] of the application.
     * - absolute path within module (e.g. "/site/index"): the view name starts with a single slash.
     *   The actual view file will be looked for under the [[Module::viewPath|view path]] of the currently
     *   active module.
     * - relative path (e.g. "index"): the actual view file will be looked for under [[viewPath]].
     *
     * If the view name does not contain a file extension, it will use the default one `.php`.
     *
     * @param string the view name.
     */
    private $template = 'index';

    public function init()
    {
        if (is_null(static::$blocks))
        {
            static::$blocks = Block::find()->orderBy(['position' => SORT_ASC, 'weight' => SORT_ASC])->all();
        }
    }

    public function run()
    {
        $path = '/' . yii::$app->request->pathInfo;
        $blocks = [];
        foreach (static::$blocks as $block)
        {
            /* @var $block \backend\models\Block */
            if ($block->position == $this->position && ($block->show_all_pages == 1 || $this->inPathList($path, $block)))
            {
                $blocks[] = $block;
            }
        }

        return $this->render($this->template, [
            'blocks' => $blocks,
        ]);
    }

    public function getViewPath()
    {
        return Yii::getAlias('@frontend/views/widgets/block');
    }


    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    private function inPathList($path, Block $block)
    {
        $result = false;
        $allowPath = explode("\r\n", $block->show_on_pages);
        if (is_array($allowPath))
        {
            $result = in_array($path, $allowPath);
        }

        return $result;
    }
}