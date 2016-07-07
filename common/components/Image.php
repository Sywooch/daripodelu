<?php

namespace common\components;

use yii;
use yii\base\InvalidParamException;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;


class Image extends yii\imagine\Image
{

    public static function proportionalResize($filename, $size, $filter = ImageInterface::FILTER_UNDEFINED)
    {
        if ( !is_string($size) || !preg_match('/^(\d+x\d*)|(\d*x\d+)$/', $size)) {
            throw new InvalidParamException('Wrong parameter of method ' . __METHOD__ . ' of class ' . __CLASS__);
        }

        list($width, $height) = explode('x', $size);
        $img = static::getImagine()->open(Yii::getAlias($filename));

        if ( !empty($width) && !empty($height)) {
            $startX = 0;
            $startY = 0;
            if ($width <= 0 || $height <= 0) {
                throw new InvalidParamException('The width and height must be greater than 0');
            }

            $sourceRatio = $img->getSize()->getWidth() / $img->getSize()->getHeight();
            $targetRatio = $width / $height;

            if ($sourceRatio > $targetRatio) {
                $resizeThumb = static::proportionalResize($filename, 'x' . $height);
                $startX = ceil($resizeThumb->getSize()->getWidth() - $width) / 2;
                $thumb = $resizeThumb->copy()->crop(new Point($startX, $startY), new Box($width, $height));
            } elseif ($sourceRatio < $targetRatio) {
                $resizeThumb = static::proportionalResize($filename, $width . 'x');
                $startY = ceil($resizeThumb->getSize()->getHeight() - $height) / 2;
                $thumb = $resizeThumb->copy()->crop(new Point($startX, $startY), new Box($width, $height));
            } else {
                $thumb = static::proportionalResize($filename, $width . 'x');
            }
        } elseif ( !empty($width)) {
            if ($width <= 0) {
                throw new InvalidParamException('The width must be greater than 0');
            }

            $ratio = $width / $img->getSize()->getWidth();

            $box = new Box($img->getSize()->getWidth(), $img->getSize()->getHeight());
            $scaleBox = $box->scale($ratio);
            $img = $img->resize($scaleBox, $filter);

            $thumb = static::getImagine()->create($scaleBox, new Color('FFF', 100));
            $thumb->paste($img, new Point(0, 0));
        } else {
            if ($height <= 0) {
                throw new InvalidParamException('The height must be greater than 0');
            }

            $ratio = $height / $img->getSize()->getHeight();

            $box = new Box($img->getSize()->getWidth(), $img->getSize()->getHeight());
            $scaleBox = $box->scale($ratio);
            $img = $img->resize($scaleBox, $filter);

            $thumb = static::getImagine()->create($scaleBox, new Color('FFF', 100));
            $thumb->paste($img, new Point(0, 0));
        }

        return $thumb;
    }

}