<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Symfony\Component\HttpFoundation\Request;

class Upload extends Admin_Controller {
    
    /**
     * @var Upload
     */
    private $uploader;
    
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
        echo json_encode($result);
    }

    /**
     * Upload video file action
     */
    public function uploadVideoFile()
    {
        $this->uploader->setRequest(Request::createFromGlobals());
        $result = $this->uploader->uploadVideoFile();
        echo json_encode($result);
    }

    /**
     * Upload video attachment action
     */
    public function uploadVideoDocs()
    {
        $this->uploader->setRequest(Request::createFromGlobals());
        $result = $this->uploader->uploadVideoDocs();
        echo json_encode($result);
    }

    /**
     * Unlink files from video
     */
    public function unlinkFromVideo()
    {
        $post = $this->input->post();
        if(!empty($post['video_id'])) {
            $video = new Video($post['video_id']);
            if (!empty($post['video_file_id'])) {
                $video->video_file_id = null;
                $video->save();
                $result['video_file_id'] = $post['video_file_id'];
            }
            if (!empty($post['file'])) {
                $file = new File($post['file'][0]);
                if ($file->exists()) {
                    $video->delete($file);
                    $result['file'] = $post['file'];
                }

            }
            $result['success'] = true;
            echo json_encode($result);
        }
    }


    
}