<?php
/**
 * File system adapter modification
 *
 * @author ajorjik   
 */
namespace Core\Service\File;
 
use Gaufrette\Adapter\Local;

class FileSystemAdapterInflect extends Local
{
    /**
     * Get adapter directory
     *
     * @return string   
     */
    public function getDirectory()
    {
        return $this->directory;
    }
    
    /**
     * Create directory
     *
     * @param string $path   
     */
    public function createDir($path)
    {
        $this->createDirectory($path);
    }
}