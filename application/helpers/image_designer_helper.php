<?php
/**
 * Created by PhpStorm.
 * User: beer
 * Date: 30.6.15
 * Time: 16.03
 */

class Image_designer {

    public static function getImages() {
        $imageDesignerImages = array();
        $imagesDir = 'public/theme/images/image-designer';
        $imagesThumpnailDir = 'public/uploads/image-designer';
        if ($handle = opendir($imagesDir)) {
            $imagine = new Imagine\Imagick\Imagine();
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (!file_exists($imagesThumpnailDir)) {
                        mkdir($imagesThumpnailDir, 0777, true);
                    }
                    if(!file_exists($imagesThumpnailDir.'/'.$file)) {
                        $image = $imagine->open($imagesDir.'/'.$file);
                        $image->resize(new \Imagine\Image\Box(128, 64))
                            ->save(APPPATH.'../'.$imagesThumpnailDir.'/'.$file);
                    }
                    $imageDesignerImages[] = array(
                        'image' => $imagesDir.'/'.$file,
                        'thumbnail' => $imagesThumpnailDir.'/'.$file
                    );
                }
            }
            closedir($handle);
        }
        return $imageDesignerImages;
    }

}