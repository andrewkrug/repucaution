<?php
/**
 * User: alkuk
 * Date: 14.04.14
 * Time: 15:04
 */

class contest extends CLI_Controller {

    public function conv()
    {

        $converter = $this->get('core.video.convertor');
        $converter->convertVideoFile(3, 'mp4');
    }
}