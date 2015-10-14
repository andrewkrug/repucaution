<?php
/**
 * Created by PhpStorm.
 * User: FleX
 * Date: 23.04.14
 * Time: 20:10
 */

namespace Core\Service\Image;


/**
 * Class Crop
 * @package Core\Service\Image
 */
/**
 * Class Crop
 * @package Core\Service\Image
 */
/**
 * Class Crop
 * @package Core\Service\Image
 */
class Crop
{
    /**
     * @var \CI_Image_lib
     */
    protected $imageLib;

    /**
     * @var array
     */
    protected $sourceInfo;

    /**
     * @var array
     */
    protected $params = array(
        'prefix' => 'cropped_', // new file will be prefixed
        'rewrite' => false, //if set to true then source image will be replaced by cropped one
        'destination' => false, //cropped image will be saved in same folder with source if false
        'scale' => array(
            'x' => false,
            'y' => false,
        ),
        'maintain_ratio' => false
    );

    /**
     * @param \CI_Image_lib $imageLib
     * @param array $options
     */
    public function __construct(\CI_Image_lib $imageLib, $options = array())
    {
        $this->imageLib = $imageLib;
        $this->params = array_merge($this->params, $options);
    }

    /**
     * Set source image
     * @param $path
     * @return $this
     */
    public function setSourceImagePath($path)
    {
        if (file_exists($path)) {
            $this->sourceInfo = pathinfo($path);
            $this->params['source_image'] = $path;
        }
        return $this;
    }

    /**
     * Set any option to CI_img_lib | will be passed to initialize method
     * Also additional options: prefix, destination and rewrite can be set by this method
     * @param $name
     * @param $value
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * Set scales to both directions relative to the original image
     * @param $scale
     * @return $this
     */
    public function setScale($scale)
    {
        $this->setScaleX($scale)
             ->setScaleY($scale);
        return $this;
    }

    /**
     * Set X(width) scale relative to the original image
     * @param float $scaleX
     * @return $this
     */
    public function setScaleX($scaleX)
    {
        $this->params['scale']['x'] = $this->getValidScale($scaleX);
        return $this;
    }

    /**
     * Set Y(height) scale relative to the original image
     * @param $scaleY
     * @return $this
     */
    public function setScaleY($scaleY)
    {
        $this->params['scale']['y'] = $this->getValidScale($scaleY);
        return $this;
    }



    /**
     * Execute cropping with current params
     * @param int $x - pixels from top
     * @param int $y - pixels from left
     * @param int $width - width from initial point in pixels
     * @param int $height - height from initial point in pixels
     * @throws \Exception
     * @return string - new file name
     */
    public function execute($x, $y, $width = null, $height = null)
    {
        if (!isset($this->params['source_image'])) {
            throw new \Exception('Cropping cannot be completed because source image is not set.');
        }
        $this->prepareParams($x, $y, $width, $height);
        $this->imageLib->initialize($this->params);
        //print_d($this->params);
        $this->imageLib->crop();

        return $this->params['new_image'];
    }

    /**
     * Prepare parameters to use with CI_image_lib
     * @param int $x
     * @param int $y
     * @param int $width
     * @param int $height
     */
    protected function prepareParams($x, $y, $width, $height)
    {
        if ($this->params['scale']['x']) {
            $x *= $this->params['scale']['x'];
            $width *= $this->params['scale']['x'];
        }
        if ($this->params['scale']['y']) {
            $y *= $this->params['scale']['y'];
            $height *= $this->params['scale']['y'];
        }
        $this->params['x_axis'] = $x;
        $this->params['y_axis'] = $y;
        $this->params['width'] = $width;
        $this->params['height'] = $height;

        if (!$this->params['rewrite']) {
            $this->params['new_image'] = $this->getNewFileName();
        }
    }

    /**
     * @return string
     */
    protected function getNewFileName()
    {
        $destination = $this->sourceInfo['dirname'];
        if (is_dir($this->params['destination'])) {
            $destination = $this->params['destination'];
        }
        return $destination
        . '/' . $this->params['prefix']
        .       $this->sourceInfo['filename']
        . '.' . $this->sourceInfo['extension'];
    }

    /**
     * @param $scale
     * @return float|int
     */
    protected function getValidScale($scale)
    {
        $scale = (float)$scale;
        if ($scale <= 0) {
            return 1;
        }
        return $scale;
    }

} 