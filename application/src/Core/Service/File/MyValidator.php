<?php
/**
 * File system adapter modification
 *
 * @author ajorjik   
 */
namespace Core\Service\File;

use FileUpload\Validator\Simple;
use FileUpload\File;

class MyValidator extends Simple
{

    const UPLOAD_ERR_BAD_EXTENSION = 2;

    public function __construct($max_size, array $allowed_file_params)
    {

        $this->allowed_extensions = $allowed_file_params['extensions'];
        parent::__construct($max_size, $allowed_file_params['mime-types']);
        $this->messages[self::UPLOAD_ERR_BAD_EXTENSION] = 'Extension of file not allowed';
    }


    public function validate($tmp_name, File $file, $current_size)
    {
        //check extension
        $fileExtension = end(explode('.', $file->name));
        if (!in_array($fileExtension, $this->allowed_extensions)) {
            $file->error = $this->messages[self::UPLOAD_ERR_BAD_EXTENSION];
            return false;
        } else {
            //check myme-type & size
            return parent::validate($tmp_name, $file, $current_size);

        }

    }
}