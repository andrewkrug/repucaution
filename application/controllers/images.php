<?php defined('BASEPATH') OR exit('No direct script access allowed');



class Images extends MY_Controller {

    protected $website_part = 'dashboard';


    public function display()
    {
        //parse image uri
        $preset = $this->uri->segment(2);
        $fileId = $this->uri->segment(3);
        $file = new File($fileId);
        if (!$file->isFileOfUser($this->c_user->id)) {
            return false;
        }
        $imageFile = $file->image->get();
        $updated = (string)$file->image->updated;
        $manipulator = $this->get('core.image.manipulator');
        if ($this->input->post()) {
            $this->cropParamUpdate($file);
            echo 'images/'.$preset.'/'.$fileId.'/'.$imageFile->updated.'?prev='.$updated;
        } else {
            $path = FCPATH.$file->path.'/'.$file->fullname;
            $output = $manipulator->getImagePreset($path, $preset, $imageFile, Arr::get($_GET, 'prev', null));

            if ($output) {
                $info = getimagesize($output);
                header("Content-Disposition: filename={$output};");
                header("Content-Type: {$info["mime"]}");
                header('Content-Transfer-Encoding: binary');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', time()) . ' GMT');
                readfile($output);
            }
        }

    }


    protected function cropParamUpdate($file)
    {
        if ($file->exists() && $post = $this->input->post()) {
            list($width, $height) = getimagesize(FCPATH.$file->path.'/'.$file->fullname);
            $image = $file->image->get();
            $image->x = $post['x']*$width;
            $image->y = $post['y']*$height;
            $image->width = $post['w']*$width;
            $image->height = $post['h']*$height;
            $image->updated = time();
            $image->save();
        }

    }

}