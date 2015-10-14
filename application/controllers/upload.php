<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Symfony\Component\HttpFoundation\Request;

class Upload extends MY_controller {
    
    /**
     * @var Upload
     */
    private $uploader;

    protected $website_part = 'dashboard';

    public function __construct()
    {
        parent::__construct();
        $this->uploader = $this->get('core.file.upload');
        $this->uploader->setUser($this->c_user);
    }
    
    /**
     * Upload file action
     */
    public function uploadFile()
    {
        $this->uploader->setRequest(Request::createFromGlobals());
        $result = $this->uploader->uploadFile();
        $this->renderJson($result);
    }

    /**
     * Upload image file action
     */
    public function uploadImageFile()
    {
        $this->uploader->setRequest(Request::createFromGlobals());
        $result = $this->uploader->uploadImageFile();
        $this->renderJson($result);
    }

    public function themeImage()
    {
        $this->uploader->setRequest(Request::createFromGlobals());
        $result = $this->uploader->uploadImageFile();
        $file = new File($result['upload_file_id']);

        $answer = array(
            'file' => array(
                'url' => $file->getUrl(),
                'id' => $file->id
            )
        );
        echo json_encode($answer);
    }

}