<?php

namespace frontend\widgets;

use yii;
use yii\base\Widget;
use frontend\models\Catalogue;

class CatalogueWidget extends Widget
{
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
    {}

    public function run()
    {
        $categories = Catalogue::find()
            ->with('photo')
            ->where(['parent_id' => 1])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return $this->render($this->template, [
            'categories' => $categories
        ]);
    }

    public function getViewPath()
    {
        return Yii::getAlias('@frontend/views/widgets/catalogue');
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}