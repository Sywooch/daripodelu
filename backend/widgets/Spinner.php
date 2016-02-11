<?php

namespace backend\widgets;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

class Spinner extends \kartik\spinner\Spinner
{
    /**
     * @var array the widths for each preset.
     */
    private $_presets = [
        self::TINY,
        self::SMALL,
        self::MEDIUM,
        self::LARGE,
    ];

    /**
     * @var boolean is the preset valid
     */
    private $_validPreset = false;

    public function init()
    {
//        parent::init();
        \yii\base\Widget::init();
        $this->_validPreset = (!empty($this->preset) && in_array($this->preset, $this->_presets));
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        // Spinner
        $tag = ArrayHelper::remove($this->spinOptions, 'tag', 'div');
        Html::addCssClass($this->spinOptions, 'kv-spin kv-spin-' . $this->align);
        $spinner = Html::tag($tag, '&nbsp;', $this->spinOptions);

        // Caption
        $tag = ArrayHelper::remove($this->captionOptions, 'tag', ($this->align == 'left' || $this->align == 'right') ? 'span' : 'div');
        Html::addCssClass($this->captionOptions, ($this->_validPreset ? "kv-spin-{$this->preset}-{$this->align}" : ''));
        $caption = trim($this->caption);
        $caption = empty($caption) ? '' : Html::tag($tag, $this->caption, $this->captionOptions);

        // Spinner + Caption
        Html::addCssClass($this->options, "kv-spin-{$this->align}" . ($this->hidden ? " kv-hide" : ""));
        $tag = ArrayHelper::remove($this->options, 'tag', 'div');
        echo Html::tag($tag, $spinner . $caption, $this->options);

        $this->registerAssets();
    }
}