<?php
/**
 * Created by PhpStorm.
 * User: Ajorjik
 * Date: 3/25/14
 * Time: 9:55 AM
 */



namespace Core\Service\Image;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Gd\Imagine;

class Manipulator
{
    /**
     * @var string
     */
    protected $inputImagePath;

    /**
     * @var string
     */
    protected $outputImagePath = '';

    /**
     * @var array
     */
    protected $presets;

    /**
     * @var string
     */
    private $preset;

    /**
     * @var \Imagine\Gd\Imagine
     */
    private $image;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var array
     */
    private $cropParams;

    /**
     * Load config
     */
    public function __construct()
    {
        get_instance()->load->config('images');
        $this->image = new Imagine();
        $this->presets = get_instance()->config->config['presets'];

    }

    /**
     * Return filepath of image (create file if it is not exist)
     *
     * @param null|string $inputImagePath
     * @param null|string $preset
     * @param null|array $cropParams
     * @return string
     */
    public function getImagePreset($inputImagePath = null, $preset = null, $imageFile, $prevUpdated = null)
    {
        if ($inputImagePath) {
            $this->setInputImagePath($inputImagePath);
        }
        if ($preset) {
            $this->setPreset($preset);
        }

        if ($this->width && $this->height) {
            $pathInfo = pathinfo($this->inputImagePath);
            $this->outputImagePath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'-'.$imageFile->updated.
                                                '-'.$this->width.'x'.$this->height.'.'.$pathInfo['extension'];
            if (!file_exists($this->outputImagePath) || $recreate) {
                $image = $this->image->open($this->inputImagePath);
                if ($imageFile->width && $imageFile->height) {
                    $image->crop(new Point($imageFile->x, $imageFile->y),
                                 new Box($imageFile->width, $imageFile->height));
                }
                    $image->resize(new Box($this->width, $this->height))
                          ->save($this->outputImagePath);

            }

            if ($prevUpdated && ($imageFile->updated > (int) $prevUpdated)) {
                $prevImagePath = $pathInfo['dirname'].'/'.$pathInfo['filename'].'-'.$prevUpdated.
                    '-'.$this->width.'x'.$this->height.'.'.$pathInfo['extension'];
                if (file_exists($prevImagePath)) {
                    unlink($prevImagePath);
                }
            }
        }

        return $this->outputImagePath;
    }

    /**
     * Set image
     *
     * @param string $imagePath
     */
    public function setImage($imagePath)
    {
        $this->image->open($imagePath);
    }

    /**
     * Set preset
     *
     * @param string $preset
     */
    public function setPreset($preset)
    {
        if (array_key_exists($preset, $this->presets)) {
            $this->preset = $preset;
            list($this->width, $this->height) = $this->presets[$this->preset];
        }

    }

    /**
     * Set params for cropping of image
     *
     * @param array $cropParams
     */
    public function setCropParams($cropParams)
    {
        $this->cropParams = $cropParams;
    }

    /**
     * Set path of input image
     *
     * @param string $inputImagePath
     */
    public function setInputImagePath($inputImagePath)
    {
        $this->inputImagePath = $inputImagePath;
    }

    /**
     * Set path of output image
     *
     * @param string $outputImagePath
     */
    public function setOutputImagePath($outputImagePath)
    {
        $this->outputImagePath = $outputImagePath;
    }


}