<?php

namespace backend\widgets;

use yii;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use common\models\MenuTree;


class InputAliasWidget extends InputWidget {

    public $sourceFieldId;
    public $url;
    /**
     * @var string the size of the input ('lg', 'md', 'sm', 'xs')
     */
    public $size;
    /**
     * @var array HTML attributes to render on the container if its used as a component.
     */
    public $containerOptions = [];
    /**
     * @var string the template to render the input.
     */
    public $template = "{input}{button}";
    /**
     * @var string the icon to use on the pickup button. For example, `glyphicon-th` or `glyphicon-time` and so on.
     */
    public $pickButtonIcon = '';

    public function init()
    {
        parent::init();

        Html::addCssClass($this->containerOptions, 'input-group col-md-8');
        Html::addCssClass($this->options, 'form-control');
        if ($this->size !== null)
        {
            $size = 'input-' . $this->size;
            Html::addCssClass($this->options, $size);
            Html::addCssClass($this->containerOptions, $size);
        }
    }

    public function run()
    {
        $this->options['id'] = isset($this->options['id'])? $this->options['id'] : 'item-alias';
        $input = $this->hasModel()
            ? Html::activeTextInput($this->model, $this->attribute, $this->options)
            : Html::textInput($this->name, $this->value, $this->options);

        $pickIcon = $this->pickButtonIcon != '' ? Html::tag('span', '', ['class' => $this->pickButtonIcon]) . ' ' : '';
        $pickerAddon = Html::tag('button', $pickIcon . Yii::t('app', 'Create'), ['class' => 'btn btn-default', 'id' => 'alias-generate', 'type' => 'button']);
        $inputGroupBtn = Html::tag('div', $pickerAddon, ['class' => 'input-group-btn']);

        if (strpos($this->template, '{button}') !== false)
        {
            $input = Html::tag(
                'div',
                strtr($this->template, ['{input}' => $input, '{button}' => $inputGroupBtn]),
                $this->containerOptions
            );
        }

        $view = $this->getView();
        $view->registerJs("
            var aliasGenerate = $('#alias-generate'),
                sourceField = $('#" . $this->sourceFieldId . "'),
                itemAlias = $('#" . $this->options['id'] . "');

            aliasGenerate.on('click', function(){
                var str = sourceField.val();

                $.ajax({
                    type: 'GET',
                    url: '" . $this->url . "',
                    dataType: 'json',
                    data: {phrase: str},
                    beforeSend: function(){
                        if ( str == '' || str == null)
                        {
                            bootbox.alert('" . Yii::t('app', 'You must fill in the name of the menu item.') . "');

                            return false;
                        }
                        else
                        {
                            itemAlias.addClass('loading');
                            aliasGenerate.addClass('loading');
                        }
                    },
                    success: function(data, textStatus, jqXHR){
                        itemAlias.val(data.rslt);
                        itemAlias.removeClass('loading');
                        aliasGenerate.removeClass('loading');
                    },
                    error: function(){
                        bootbox.alert('" . Yii::t('app', 'An error occurred while updating!') . "');
                        itemAlias.removeClass('loading');
                        aliasGenerate.removeClass('loading');
                    },
                    statusCode: {
                        404: function() {
                          bootbox.alert('" . Yii::t('app', 'Page not found!') . "');
                        },
                        500: function() {
                          bootbox.alert('" . Yii::t('app', 'Internal server error!') . "');
                        },
                    }
                });
            });
        ");

        echo $input;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }
}