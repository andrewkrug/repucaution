<?php
/**
 * Service for file uploading
 *
 * @author ajorjik
 */
 namespace Core\Service\File;

 use Gaufrette\FileSystem;
 use FileUpload\FileUpload;
 use FileUpload\File as FileUploadFile;
 use FileUpload\PathResolver\Simple as PathResolver;
 use FileUpload\FileSystem\Simple as FileUploaderSystem;
 use Symfony\Component\HttpFoundation\Request;
 use Symfony\Component\HttpFoundation\FileBag;
 use Core\Service\File\MyValidator as Validator;
 use File;
 use Video_file;
 use Closure;
 use Image;
 use CI;
 use User;

 class Upload
 {
    /**
     * @var FileSystem
     */
    protected $localFileSystem;

    /**
     * @var FileSystem
     */
    protected $remoteFileSystem;

    /**
     * @var string temporaly path for file uploading
     */
    protected $tempPath;

    /**
     * @var string destination path for file uploading
     */
    protected $destPath;

    /**
     * @var string basic path of file system
     */
    protected $basicPath;

    /**
     * @var array accept types of video files
     */
    protected $acceptFileTypes;

    /**
     * @var Validator file validator
     */
    protected $validator;

    /**
     * @var int max size of file for uploading
     */
    protected $maxFileSize;

    /**
     * @var string attribute name in post array
     */
    protected $fileIdAttr = 'upload_file_id';

     /**
      * @var string name of file field
      */
     protected $fileField = 'file';

     /**
      * @var User
      */
     protected $user;

    /**
     * @var Request
     */
    protected $request;

     /**
      * @var array
      */
     protected $lastUploadedResult;

    public function __construct(FileSystem $localFileSystem, FileSystem $remoteFileSystem, CI $codeIgniter, \User $currentUser)
    {
        $this->localFileSystem = $localFileSystem;//gaufrette file system
        $this->remoteFileSystem = $remoteFileSystem;//gaufrette file system
        $this->basicPath = $this->localFileSystem->getAdapter()->getDirectory();

        $codeIgniter->load->config('upload');

        $this->tempPath = $codeIgniter->config->config['temp_upload_file_path'];
        $this->destPath = $codeIgniter->config->config['dest_upload_file_path'];


        $this->acceptFileTypes =  $codeIgniter->config->config['default_file_types'];
        $this->maxFileSize =  $codeIgniter->config->config['default_max_file_size'];
        $this->validator = new Validator($this->maxFileSize, $this->acceptFileTypes);

        $this->setUser($currentUser);
    }

     /**
      * Set user
      *
      * @param User $user
      */
     public function setUser($user)
     {
        $this->user = $user;
     }

    /**
     * Uploading files
     *
     * @return array
     */
    public function uploadFile()
    {
        return $this->process();
    }

     /**
      * Uploading video files
      *
      * @return array
      */
     public function uploadVideoFile()
     {

         $callback = function($file)
         {
             $videoFile = new Video_file();
             $videoFile->save($file);
             return  array('video_file_id' => $videoFile->id);

         };
         $this->acceptFileTypes =  get_instance()->config->config['video_file_types'];
         $this->maxFileSize =  get_instance()->config->config['max_video_file_size'];
         $this->destPath = get_instance()->config->config['dest_upload_video_path'];
         $validator = new Validator($this->maxFileSize, $this->acceptFileTypes);
         $result = $this->process($callback, $validator);

         return $result;
     }

     /**
      * Uploading video files
      *
      * @return array
      */
     public function uploadImageFile()
     {
         $callback = function($file)
         {
             $image = new Image();
             $image->updated = time();
             $image->save($file);
             return  array('image_update' => $image->updated, 'url' => $file->getPathAndName().'.'.$file->ext);
         };
         $this->acceptFileTypes =  get_instance()->config->config['image_file_types'];
         $this->maxFileSize =  get_instance()->config->config['max_image_file_size'];
         $this->destPath = get_instance()->config->config['dest_upload_images_path'];
         $validator = new Validator($this->maxFileSize, $this->acceptFileTypes);
         $result = $this->process($callback, $validator);

         return $result;
     }

     /**
      * Uploading video attachments
      *
      * @return array
      */
     public function uploadVideoDocs()
     {
         $this->acceptFileTypes =  get_instance()->config->config['doc_file_types'];
         $this->maxFileSize =  get_instance()->config->config['max_doc_file_size'];
         $this->destPath = get_instance()->config->config['dest_upload_attachments_path'];
         $validator = new Validator($this->maxFileSize, $this->acceptFileTypes);

         return $this->process(null, $validator);
     }

    /**
     * Process of uploading file
     *
     * @param Closure $callback
     * @param Validator $validator
     * @return array
     */
    public function process(Closure $callback = null, Validator $validator = null)
    {

        $fileId = $this->request->get($this->fileIdAttr);
        $files = $this->request->files;

        if (!$fileId) {
            $file = new File();
            $file->user_id = $this->user->id;
            $file->addNewFromFiles($files);
            $fileId = $file->id;
        }

        $localFileSystem = $this->localFileSystem;
        $remoteFileSystem = $this->remoteFileSystem;
        $tempPath = $this->tempPath.'/'.$fileId;
        $destPath = $this->destPath.'/'.$fileId;
        $tempAbsolutePath = $this->basicPath.$this->tempPath.'/'.$fileId;

        (!$localFileSystem->has($tempPath)) ?
            $localFileSystem->getAdapter()->createDir($tempAbsolutePath) :
            '';

        if (!$validator) {
            $validator = $this->validator;
        }
        $fileSystem = new FileUploaderSystem();
        $pathResolver = new PathResolver($this->basicPath.$this->tempPath.'/'.$fileId);

        $filesArray = $this->getFilesInfoArray($files);
        $serverArray = $this->request->server->all();
        $fileUploader = new FileUpload($filesArray['file'], $serverArray);
        $fileUploader->setPathResolver($pathResolver);
        $fileUploader->setFileSystem($fileSystem);
        $fileUploader->addValidator($validator);
        $callbackResult = null;

        //callback
        $fileUploader->addCallback('completed', function(FileUploadFile $files)
            use ($callback, $fileId, $tempPath, $destPath, $localFileSystem, $remoteFileSystem, &$callbackResult){
                $file = new File($fileId);
                $file->path = $destPath;
                $file->status = $file::STATUS_UPLOADED;
                $file->size = $localFileSystem->get($tempPath.'/'.$files->name)->getSize();
                $file->save();
                $fileContent = $localFileSystem->read($tempPath.'/'.$files->name);
                $remoteFileSystem->write($destPath.'/'.$files->name, $fileContent);
                $localFileSystem->delete($tempPath.'/'.$files->name);
                $localFileSystem->delete($tempPath);
                if ($callback) {
                    $callbackResult = $callback($file);
                }
        });

        list($fileUploadObject, $headers) = $fileUploader->processAll();
        $properties = get_object_vars($fileUploadObject[0]);

       // O_O mama mia
       /* foreach ($properties as $property=> $value) {
            if ($property != 'path') {
                $info[$property] = $value;
            }
        } */
        unset($properties['path']);

        $result = array('info' => $properties);
        if ($result['info']['error'] != 0) {
            $file->delete();
        }
        if ($callbackResult) {
            $result = array_merge($result, $callbackResult);
        }
        $result[$this->fileIdAttr] = $fileId;
        $this->lastUploadedResult = $result;
        return $result;

    }


    /**
     * Set name of attribute which contain file id
     *
     * @param string $fileIdAttr
     */
    public function setFileIdAttr($fileIdAttr)
    {
        $this->fileIdAttr = $fileIdAttr;
    }

     /**
      * Set name of file field
      *
      * @param string $fileField
      */
     public function setFileField($fileField)
     {
         $this->fileField = $fileField;
     }

    /**
     * Set request object for uploader
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

     /**
      * Retrieve id of the model of the last uploaded file
      * @return int|null
      */
     public function getLastUploadedFileId()
    {
        return @$this->lastUploadedResult[$this->fileIdAttr];
    }

    /**
     * Get array of file info from file object
     *
     * @return array
     */
    protected function getFilesInfoArray(FileBag $files)
    {
        $filesArray = array();
        $file = $files->get($this->fileField);
        $fileInfo = array();
        $fileInfo['name'] = $file->getClientOriginalName();
        $fileInfo['type'] = $file->getMimeType();
        $fileInfo['tmp_name'] = $file->getPathName();
        $fileInfo['error'] = $file->getError();
        $fileInfo['size'] = $file->getSize();
        $filesArray['file'] = $fileInfo;

        return $filesArray;
    }

}