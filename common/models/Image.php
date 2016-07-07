<?php

namespace common\models;

use common\components;
use common\components\ActiveRecord;
use dosamigos\transliterator\TransliteratorHelper;
use yii;
use yii\data\ActiveDataProvider;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%image}}".
 *
 * @property string $id
 * @property string $model
 * @property integer $ctg_id
 * @property string $owner_id
 * @property string $file_name
 * @property \yii\web\UploadedFile $file
 * @property integer $title
 * @property integer $description
 * @property integer $is_main
 * @property integer $weight
 * @property integer $status
 * @property integer $maxWidth
 * @property integer $maxHeight
 * @property string $url
 */
class Image extends ActiveRecord
{

    const NOT_MAIN = 0;
    const IS_MAIN = 1;

    protected $file;
    protected $maxWidth;
    protected $maxHeight;

    public function __construct($config = [])
    {
        $this->setMaxWidth(Yii::$app->params['imageMaxWidth']);
        $this->setMaxHeight(Yii::$app->params['imageMaxHeight']);

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'safe'],
            [['file'], 'file', 'extensions' => 'jpg, gif, png, jpeg'],
            [['file'], 'file', 'skipOnEmpty' => true],
            [['file'], 'file', 'maxSize' => yii::$app->params['maxImgFileSize']],
            [['model', 'owner_id', 'file_name'], 'required'],
            [['ctg_id', 'owner_id', 'is_main', 'weight', 'status'], 'integer'],
            [['model'], 'string', 'max' => 25],
            [['file_name', 'title'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
            [['file_name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'model' => Yii::t('app', 'Название модели'),
            'ctg_id' => Yii::t('app', 'ID категории'),
            'owner_id' => Yii::t('app', 'ID владельца'),
            'file_name' => Yii::t('app', 'Имя файла'),
            'title' => Yii::t('app', 'Название фотографии'),
            'description' => Yii::t('app', 'Описание к фотографии'),
            'is_main' => Yii::t('app', 'Главное фото'),
            'weight' => Yii::t('app', 'Приоритет'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * @param string $model Name of model - character code that identifies model which images is owned. For example, news, article and etc.
     * @param int $owner Id of a news, a page and etc.
     * @param int $category Id of category to which a news, a page or any other item belongs.
     * @return static[] Array of Image instances
     */
    public function getFiles($model, $owner, $category = 0)
    {
        return $this->findAll([
            'model' => strval($model),
            'owner_id' => intval($owner),
            'ctg_id' => intval($category),
        ]);
    }

    /**
     * @param string $model Name of model - character code that identifies model which images is owned. For example, news, article and etc.
     * @param int $owner Id of a news, a page and etc.
     * @param int $category Id of category to which a news, a page or any other item belongs.
     * @return ActiveDataProvider
     */
    public function getDataProvider($model, $owner, $category = 0)
    {
        $query = Image::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['weight' => SORT_ASC],
            ],
        ]);

        $query->andFilterWhere([
            'model' => strval($model),
            'owner_id' => intval($owner),
            'ctg_id' => intval($category),
        ]);

        return $dataProvider;
    }

    /**
     * @return int Returns maximum height of image
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Sets the maximum height of image
     *
     * @param int $maxHeight
     */
    public function setMaxHeight($maxHeight)
    {
        if ( !is_integer($maxHeight)) {
            throw new yii\base\InvalidArgumentException('Max height must be integer');
        } else {
            if ($maxHeight <= 0) {
                throw new yii\base\InvalidArgumentException('Max height must be greater than 0');
            }
        }

        $this->maxHeight = $maxHeight;
    }

    /**
     * @return int Returns maximum width of image
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Sets the maximum width of image
     *
     * @param int $maxWidth
     */
    public function setMaxWidth($maxWidth)
    {
        if ( !is_integer($maxWidth)) {
            throw new yii\base\InvalidArgumentException('Max width must be integer');
        } else {
            if ($maxWidth <= 0) {
                throw new yii\base\InvalidArgumentException('Max width must be greater than 0');
            }
        }

        $this->maxWidth = $maxWidth;
    }

    /**
     * Deletes the table row corresponding to this active record.
     *
     * @return false|int the number of rows deleted, or false if the deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the deletion execution is successful.
     * @throws \Exception in case delete failed.
     */
    public function delete()
    {
        $rslt = parent::delete();
        if ($rslt) {
            if ($this->is_main) {
                $this->setMainDefault();
            }
            $this->deleteFiles();
        }

        return $rslt;
    }

    /**
     * Deletes all files of owner, for example, of news or article
     *
     * @param string $model Name of model - character code that identifies model which images is owned. For example,
     * news, article and etc.
     * @param int $ownerId Id of a news, a page and etc.
     * @param int $ctgId Id of category to which a news, a page or any other item belongs.
     *
     * @return $deletedItemsCnt int Number of deleted items
     */
    public static function deleteAllFilesOfOwner($model, $ownerId, $ctgId)
    {
        $model = strval($model);
        $ownerId = intval($ownerId);
        $ctgId = intval($ctgId);

        $imageModels = Image::find()->where(
            'model = :model and owner_id = :owner_id and ctg_id = :ctg_id',
            [
                ':model' => $model,
                ':owner_id' => $ownerId,
                ':ctg_id' => $ctgId,
            ]
        )->all();

        if (count($imageModels)) {
            $deletedItemsCnt = Image::deleteAll(
                'model = :model and owner_id = :owner_id and ctg_id = :ctg_id',
                [
                    ':model' => $model,
                    ':owner_id' => $ownerId,
                    ':ctg_id' => $ctgId,
                ]
            );

            if ($deletedItemsCnt > 0) {
                /* @var $imageModel Image */
                foreach ($imageModels as $imageModel) {
                    $imageModel->deleteFiles();
                }
            }
        }

        return $deletedItemsCnt;
    }

    public function getModelDirPath()
    {
        return Yii::$app->params['uploadPath'] . '/' . $this->model;
    }

    /**
     * @return string Returns the absolute path to the upload directory of files owner
     */
    public function getImageDirPath()
    {
        return $this->getModelDirPath() . '/fldr_' . intval($this->ctg_id) . '_' . intval($this->owner_id);
    }

    /**
     * @return string Absolute path to the upload directory of thumbnail files owner
     */
    public function getImageTmbDirPath()
    {
        return $this->getImageDirPath() . '/.tmb';
    }

    function getUrl()
    {
        return $this->_getImageUrl();
    }

    /**
     * Sets the main image
     * @return bool|int The number of rows affected, or false if validation fails or beforeSave() stops the updating process.
     * @throws \Exception
     */
    public function setMain()
    {
        $this->updateAll(
            ['is_main' => Image::NOT_MAIN],
            [
                'model' => $this->model,
                'owner_id' => $this->owner_id,
                'ctg_id' => $this->ctg_id,
            ]
        );

        $this->is_main = Image::IS_MAIN;
        $rslt = $this->save(true, ['is_main']);

        return $rslt;
    }

    /**
     * Sets the main default image
     *
     * @return bool
     */
    protected function setMainDefault()
    {
        $model = Image::find()->where('model = :model and owner_id = :owner_id and ctg_id = :ctg_id and status = 1 and is_main <> 1', [':model' => $this->model, ':owner_id' => $this->owner_id, ':ctg_id' => $this->ctg_id])->orderBy('weight ASC')->one();
        $rslt = false;

        if ($model) {
            $model->is_main = Image::IS_MAIN;
            $rslt = $model->save(true, ['is_main']);
        }

        return $rslt;
    }

    public function __get($name)
    {
        if (preg_match('/^image_url_\d+x\d+/', $name)) {
            $tmp = explode('_', $name);
            list($width, $height) = explode('x', $tmp[2]);

            if ( !file_exists($this->_getImagePath(['width' => $width, 'height' => $height]))) {
                if ( !file_exists($this->getImageTmbDirPath())) {
                    mkdir($this->getImageTmbDirPath(), 0777, true);
                }

                yii\imagine\Image::$driver = \yii\imagine\Image::DRIVER_GD2;
                yii\imagine\Image::thumbnail($this->_getImagePath(), $width, $height)->save($this->_getImagePath(['width' => $width, 'height' => $height]));
            }

            return $this->_getImageUrl(['width' => $width, 'height' => $height]);
        } else {
            return parent::__get($name);
        }
    }

    /**
     * Returns an uploaded file
     * @return \yii\web\UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param \yii\web\UploadedFile $file
     */
    public function setFile(yii\web\UploadedFile $file)
    {
        $this->file = $file;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $trans = $this->getDb()->beginTransaction();
        try {
            if (parent::save($runValidation, $attributeNames)) {
                if ($this->file) {
                    if ( !file_exists($this->getModelDirPath())) {
                        mkdir($this->getModelDirPath(), 0777, true);
                    }

                    if ( !file_exists($this->getImageDirPath())) {
                        mkdir($this->getImageDirPath(), 0777, true);
                    }

                    components\Image::$driver = components\Image::DRIVER_GD2;
                    $img = components\Image::getImagine()->open($this->file->tempName);
                    if ($img->getSize()->getWidth() > $this->getMaxWidth() || $img->getSize()->getHeight() > $this->getMaxHeight()) {

                        if (components\Image::proportionalResize($this->file->tempName, $this->getMaxSizeString())->save($this->_getImagePath())) {
                            $trans->commit();

                            return true;
                        } else {
                            throw new \Exception('s');
                        }
                    } else {
                        $this->file->saveAs($this->_getImagePath());
                    }
                }

                if ( !$this->isNewRecord) {
                    $trans->commit();

                    return true;
                }
            }
        }
        catch (\Exception $e) {
            $trans->rollBack();
        }

        return false;
    }

    protected function getMaxSizeString()
    {
        if (isset($this->maxWidth) && isset($this->maxHeight)) {
            return $this->getMaxWidth() . 'x' . $this->getMaxHeight();
        } else {
            throw new yii\base\InvalidValueException('Invalid value');
        }
    }

    protected function _getImagePath($options = null)
    {
        if (is_array($options)) {
            list($fname, $ext) = explode('.', $this->file_name);
            $path = $this->getImageTmbDirPath() . '/' . $fname . '_' . $options['width'] . 'x' . $options['height'] . '.' . $ext;
        } else {
            $path = $this->getImageDirPath() . '/' . $this->file_name;
        }

        return $path;
    }

    protected function _getImageUrl($options = null)
    {
        if (is_array($options)) {
            list($fname, $ext) = explode('.', $this->file_name);
            $url = Yii::$app->params['baseUploadURL'] . '/' . $this->model . '/fldr_' . intval($this->ctg_id) . '_' . intval($this->owner_id) . '/.tmb/' . $fname . '_' . $options['width'] . 'x' . $options['height'] . '.' . $ext;
        } else {
            $url = Yii::$app->params['baseUploadURL'] . '/' . $this->model . '/fldr_' . intval($this->ctg_id) . '_' . intval($this->owner_id) . '/' . $this->file_name;
        }

        return $url;
    }

    public function deleteFiles()
    {
        unlink($this->_getImagePath());
        $pathParts = pathinfo($this->file_name);

        $tmbImages = FileHelper::findFiles(
            $this->getImageTmbDirPath(),
            [
                'only' => [$pathParts['filename'] . '*.*'],
            ]
        );

        foreach ($tmbImages as $file) {
            unlink($file);
        }
    }

    public function deleteFolderIfEmpty()
    {
        if (file_exists($this->getImageTmbDirPath())) {
            $tmbImages = FileHelper::findFiles(
                $this->getImageTmbDirPath(),
                [
                    'only' => ['*.jpg', '*.gif', '*.png', '*.jpeg'],
                ]
            );
        }

        if (file_exists($this->getImageDirPath())) {
            $origImages = FileHelper::findFiles(
                $this->getImageDirPath(),
                [
                    'only' => ['*.jpg', '*.gif', '*.png', '*.jpeg'],
                ]
            );

            if (count($tmbImages) == 0 && count($origImages) == 0) {
                FileHelper::removeDirectory($this->getImageDirPath());
            }
        }
    }

    public static function generateFileName($name)
    {
        $pathParts = pathinfo($name);
        $string = TransliteratorHelper::process($pathParts['filename']);
        $string = preg_replace('/[ \-\.]+/', '_', $string);
        $string = preg_replace('/[_]+/', '_', $string);
        $string = mb_substr($string, 0, 8);
        $string = $string . '_' . strval(time()) . rand(1, 999);

        return $string . '.' . $pathParts['extension'];
    }
}
