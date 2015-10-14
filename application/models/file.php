<?php

class File extends DataMapper {
    
    const STATUS_NEW = 0;
    const STATUS_UPLOADED = 1;
    
    var $table = 'file';    
    
    var $has_one = array('video_file', 'image');

    var $has_many = array('video');
    
    var $cascade_delete = FALSE;
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('required', 'trim'),
        ),
        'path' => array(
            'label' => 'Path',
            'rules' => array('trim'),
        ),
               
    );
    
    /**
     * @var FileSystem
     */
    private $fileSystem;
    
    /**
     * Adding new files from Request in begin of uploading
     *
     * @param file $files   
     */
    public function addNewFromFiles($files)
    {
        $file = $files->get('file');
        $fileFullName = $file->getClientOriginalName();
        $this->status = self::STATUS_NEW;
        $this->fullname = $fileFullName;
        $fileParts = explode(".", $fileFullName);
        $this->ext = end($fileParts);
        $this->name = str_replace('.'.$this->ext, '', $fileFullName);
        $this->created = time();
        $this->mime = $file->getMimeType();
        $this->size = $file->getSize();
        $this->save();
        return $this;
    }

    /**
     * Get files from array of ids
     *
     * @param array $fileIds
     * @return this
     */
    public function getByIds($fileIds)
    {
        return $this->where_in('id', $fileIds)->get();
    }

    public function getImageCrop()
    {
       // $result = null;
        $image = $this->image->get();
        //if ($image->width && $image->height) {
            $result = array(
                $image->x,
                $image->y,
                $image->width,
                $image->height,
                $image->update
            );
       // }

        return $result;
    }

    /**
     * Get file path and file name
     *
     * @return string
     */
    public function getPathAndName()
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->name;
    }

    /**
     * Get full path to file (not absolute)
     *
     * @return string
     */
    public function getFullPathAndName()
    {
        return $this->path.DIRECTORY_SEPARATOR.$this->fullname;
    }

    /**
     * Check file of user
     *
     * @param int $userId
     */
    public function isFileOfUser($userId)
    {
        return ($this->user_id == $userId);
    }

}