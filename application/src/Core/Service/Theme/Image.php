<?php
/**
 * Created by PhpStorm.
 * User: FleX
 * Date: 23.04.14
 * Time: 12:22
 */

namespace Core\Service\Theme;


use \File;
/**
 * Class Image
 * @package Core\Service\Theme
 */
class Image implements \Ikantam\Theme\Interfaces\ImageInterface
{
    /**
     * @var array
     */
    protected $data;

    protected $source;

    /**
     * @param mixed $source
     * @throws \Exception
     */
    public function __construct($source)
    {
        $this->source = $source;
        if ($source instanceof File) {
            $this->data = array(
                'url' => site_url($source->path . '/' . $source->fullname),
                'path' => realpath(APPPATH . '../' . $source->path . '/' . $source->fullname),
            );
        } elseif (is_array($source)) {

            $this->data = array(
                'url' => \Kohana_Arr::getValidate($source, 'url', null, function($val){
                            if (!is_string($val)) {
                                throw new \Exception(__method__ . ' url must be a string.');
                            }
                            return true;
                        }),
                'path' => \Kohana_Arr::getValidate($source, 'path', null, function($val){
                            if (!is_string($val)) {
                                throw new \Exception(__method__ . ' path must be a string.');
                            }
                            return true;
                        })
            );
        } else {
            throw new \Exception('Invalid source given. Expected array or \File ' . gettype($source) . ' given.');
        }

    }

    /**
     * Return image url address
     * @return string
     */
    public function getUrl()
    {
        return @$this->data['url'];
    }

    /**
     * Return image physical path
     * @return string
     */
    public function getPath()
    {
        return @$this->data['path'];
    }

    /**
     * Delete an image from file system
     * @return bool
     */
    public function delete()
    {
        $path = $this->getPath();
        if ($path && file_exists($path)) {
            unlink($path);
            return true;
        }
        return false;
    }

    /**
     * Get image data array
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
