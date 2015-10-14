<?php
/**
 * Created by PhpStorm.
 * User: FleX
 * Date: 23.04.14
 * Time: 12:14
 */

namespace Core\Service\Theme\Image;


use Core\Service\File\Upload;
use Core\Service\Theme\Image as TemplateImage;
use Ikantam\Theme\Interfaces\Image\UploaderInterface;
use Ikantam\Theme\Interfaces\Image;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class Uploader
 * @package Core\Service\Theme\Image
 */
class Uploader implements UploaderInterface
{
    /**
     * @var \Core\Service\File\Upload
     */
    protected $uploader;

    /**
     * @param Upload $uploader
     */
    public function __construct(Upload $uploader)
    {
        $this->uploader = $uploader;
        $this->uploader->setRequest(Request::createFromGlobals());
    }

    /**
     * Uploads image file
     * @return Image
     */
    public function upload()
    {
        $this->uploader->uploadImageFile();
        return $this->createImage($this->createFileModel($this->uploader->getLastUploadedFileId()));
    }

    /**
     * Get file model
     * @param $id
     * @return \File
     * @throws \Exception
     */
    protected function createFileModel($id)
    {
        $model = new \File($id);
        if (!$model->exists()) {
            throw new \Exception('File info record is not exist. ID = ' . $id);
        }
        return $model;
    }

    /**
     * Create image class using file model
     * @param \File $model
     * @return TemplateImage
     */
    protected function createImage(\File $model)
    {
        return new TemplateImage($model);
    }
}