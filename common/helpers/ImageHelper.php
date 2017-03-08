<?php
/**
 * Created by PhpStorm.
 * User: bubasuma
 * Date: 3/11/16
 * Time: 4:20 PM
 */

namespace common\helpers;


use Yii;
use Imagine\Image\Box;
use Imagine\Image\BoxInterface;
use Imagine\Image\Color;
use Imagine\Image\ImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use yii\base\InvalidParamException;
use yii\imagine\BaseImage;

class ImageHelper extends BaseImage
{
    /**
     * @var string background color to use when creating thumbnails in `ImageInterface::THUMBNAIL_INSET` mode with
     * both width and height specified. Default is white.
     *
     * @since 2.0.4
     */
    public static $thumbnailBackgroundColor = 'FFF';
    /**
     * @var string background alpha (transparency) to use when creating thumbnails in `ImageInterface::THUMBNAIL_INSET`
     * mode with both width and height specified. Default is solid.
     *
     * @since 2.0.4
     */
    public static $thumbnailBackgroundAlpha = 100;

    /**
     * Crops an image.
     *
     * For example,
     *
     * ~~~
     * $obj->crop('path\to\image.jpg', 200, 200, [5, 5]);
     *
     * $point = new \Imagine\Image\Point(5, 5);
     * $obj->crop('path\to\image.jpg', 200, 200, $point);
     * ~~~
     *
     * @param string $filename the image file path or path alias.
     * @param integer $width the crop width
     * @param integer $height the crop height
     * @param array $start the starting point. This must be an array with two elements representing `x` and `y` coordinates.
     * @return ImageInterface
     * @throws InvalidParamException if the `$start` parameter is invalid
     */
    public static function crop($filename, $width, $height, array $start = [0, 0])
    {
        $img = static::getImagine()->open(Yii::getAlias($filename));
        $sourceBox = $img->getSize();
        $cropBox = static::getCropBox($sourceBox, $width, $height);
        return $img->copy()->crop(new Point($start[0], $start[1]), $cropBox);
    }

    /**
     * @param BoxInterface $sourceBox
     * @param int $width
     * @param int $height
     * @return Box
     */
    protected static function getCropBox(BoxInterface $sourceBox, $width, $height){
        if ($width !== null && $height !== null) {
            return new Box($width, $height);
        }
        if ($width === null && $height === null) {
            throw new InvalidParamException('Width and height cannot be null at same time.');
        }

        if ($height === null) {
            $height = $sourceBox->getHeight();;
        } else {
            $width = $sourceBox->getWidth();;
        }
        return new Box($width, $height);
    }

    /**
     * Creates a thumbnail image.
     *
     * If one of thumbnail dimensions is set to `null`, another one is calculated automatically based on aspect ratio of
     * original image. Note that calculated thumbnail dimension may vary depending on the source image in this case.
     *
     * If both dimensions are specified, resulting thumbnail would be exactly the width and height specified. How it's
     * achieved depends on the mode.
     *
     * If `ImageInterface::THUMBNAIL_OUTBOUND` mode is used, which is default, then the thumbnail is scaled so that
     * its smallest side equals the length of the corresponding side in the original image. Any excess outside of
     * the scaled thumbnail’s area will be cropped, and the returned thumbnail will have the exact width and height
     * specified.
     *
     * If thumbnail mode is `ImageInterface::THUMBNAIL_INSET`, the original image is scaled down so it is fully
     * contained within the thumbnail dimensions. The rest is filled with background that could be configured via
     * [[Image::$thumbnailBackgroundColor]] and [[Image::$thumbnailBackgroundAlpha]].
     *
     * @param string $filename the image file path or path alias.
     * @param integer $width the width in pixels to create the thumbnail
     * @param integer $height the height in pixels to create the thumbnail
     * @param string $mode mode of resizing original image to use in case both width and height specified
     * @return ImageInterface
     */
    public static function thumbnail($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND)
    {
        $img = static::getImagine()->open(Yii::getAlias($filename));
        $sourceBox = $img->getSize();
        $thumbnailBox = static::getThumbnailBox($sourceBox, $width, $height);
        if (($sourceBox->getWidth() <= $thumbnailBox->getWidth() && $sourceBox->getHeight() <= $thumbnailBox->getHeight()) || (!$thumbnailBox->getWidth() && !$thumbnailBox->getHeight())) {
            return $img->copy();
        }
        $img = $img->thumbnail($thumbnailBox, $mode);
        // create empty image to preserve aspect ratio of thumbnail
        $thumb = static::getImagine()->create($thumbnailBox, new Color(static::$thumbnailBackgroundColor, static::$thumbnailBackgroundAlpha));
        // calculate points
        $startX = 0;
        $startY = 0;
        if ($sourceBox->getWidth() < $width) {
            $startX = ceil($width - $sourceBox->getWidth()) / 2;
        }
        if ($sourceBox->getHeight() < $height) {
            $startY = ceil($height - $sourceBox->getHeight()) / 2;
        }
        $thumb->paste($img, new Point($startX, $startY));
        return $thumb;
    }

    /**
     * Returns box for a thumbnail to be created. If one of the dimensions is set to `null`, another one is calculated
     * automatically based on width to height ratio of original image box.
     *
     * @param BoxInterface $sourceBox original image box
     * @param int $width thumbnail width
     * @param int $height thumbnail height
     * @return BoxInterface thumbnail box
     *
     * @throws InvalidParamException
     * @since 2.0.4
     */
    protected static function getThumbnailBox(BoxInterface $sourceBox, $width, $height)
    {
        if ($width !== null && $height !== null) {
            return new Box($width, $height);
        }
        if ($width === null && $height === null) {
            throw new InvalidParamException('Width and height cannot be null at same time.');
        }
        $ratio = $sourceBox->getWidth() / $sourceBox->getHeight();

        // preserve the original width if it lover than the max-width
        if($width > $sourceBox->getWidth()){
            $width = $sourceBox->getWidth();
        }

        // preserve the original height if it lover than the max-height
        if($height > $sourceBox->getHeight()){
            $height = $sourceBox->getHeight();
        }

        if ($height === null) {
            $height = ceil($width / $ratio);
        } else {
            $width = ceil($height * $ratio);
        }
        return new Box($width, $height);
    }
}