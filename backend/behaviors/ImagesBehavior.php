<?php

namespace backend\behaviors;

use common\components\ActiveRecord;
use yii;
use yii\base\Behavior;
use common\models\Image;

//TODO-cms Возвращать сообщения об ошибках при неудачной загрузки файлов

/**
 * @property string $model
 * @property integer $ownerIdAttribute
 * @property string $ctgIdAttribute
 */
class ImagesBehavior extends Behavior
{

    private $model;
    private $ownerIdAttribute = 'id';
    private $ctgIdAttribute = 'cat_id';
    private $maxWidth;
    private $maxHeight;

    public function __construct($config = [])
    {
        $this->setMaxWidth(Yii::$app->params['imageMaxWidth']);
        $this->setMaxHeight(Yii::$app->params['imageMaxHeight']);

        parent::__construct($config);
    }

    public function getImages()
    {
        $image = new Image;

        return $image->getFiles($this->model, $this->owner->{$this->ownerIdAttribute}, $this->getCtgId());
    }

    public function getDataProvider()
    {
        $image = new Image;

        return $image->getDataProvider($this->model, $this->owner->{$this->ownerIdAttribute}, $this->getCtgId());
    }

    public function setModel($model)
    {
        $this->model = strval($model);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setOwnerIdAttribute($attributeName)
    {
        $this->ownerIdAttribute = strval($attributeName);
    }

    public function getOwnerIdAttribute()
    {
        return $this->ownerIdAttribute;
    }

    public function setCtgIdAttribute($attributeName)
    {
        $this->ctgIdAttribute = strval($attributeName);
    }

    public function getCtgIdAttribute()
    {
        return $this->ctgIdAttribute;
    }

    public function getCtgId()
    {
        return ( !isset($this->owner->{$this->ctgIdAttribute}) || is_null($this->owner->{$this->ctgIdAttribute})) ? 0 : $this->owner->{$this->ctgIdAttribute};
    }

    /**
     * @return int
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * @param int $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        if ( !is_integer($maxWidth))
        {
            throw new yii\base\InvalidArgumentException('Max width must be integer');
        }
        else
        {
            if ($maxWidth <= 0)
            {
                throw new yii\base\InvalidArgumentException('Max width must be greater than 0');
            }
        }

        $this->maxWidth = $maxWidth;
    }

    /**
     * @return int
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * @param int $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        if ( !is_integer($maxHeight))
        {
            throw new yii\base\InvalidArgumentException('Max height must be integer');
        }
        else
        {
            if ($maxHeight <= 0)
            {
                throw new yii\base\InvalidArgumentException('Max height must be greater than 0');
            }
        }

        $this->maxHeight = $maxHeight;
    }

    public function getMaxFileSize()
    {
        $model = new Image();
        $validators = $model->getActiveValidators('file');

        return 600000;
    }

    public function saveImage(yii\web\UploadedFile $file)
    {
        $rslt = false;

        $model = new Image();
        $model->model = $this->model;
        $model->owner_id = $this->owner->{$this->ownerIdAttribute};
        $model->ctg_id = $this->getCtgId();
        $model->file = $file;
        $model->file_name = Image::generateFileName($file->name);
        $model->title = $file->name;
        $model->is_main = 0;
        $model->status = 1;
        $model->weight = 0;
        $model->maxWidth = $this->maxWidth;
        $model->maxHeight = $this->maxHeight;

        if ($model->file && $model->validate())
        {
            $rslt = $model->save() ? $model : false;
        }

        return $rslt;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => function ($event)
            {
                Image::deleteAllFilesOfOwner($this->model, $this->owner->{$this->ownerIdAttribute}, $this->getCtgId());

                $imageModel = new Image();
                $imageModel->model = $this->model;
                $imageModel->owner_id = $this->owner->{$this->ownerIdAttribute};
                $imageModel->ctg_id = $this->getCtgId();

                $imageModel->deleteFolderIfEmpty();
            },
        ];
    }
}
