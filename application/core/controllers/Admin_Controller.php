<?php
/**
 * User: dev
 * Date: 16.01.14
 * Time: 15:07
 */

require_once __DIR__.'/MY_Controller.php';

class Admin_Controller extends MY_Controller
{

    public function __construct() {
        parent::__construct($this->website_part);
        $this->lang->load('admin_global', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('admin_global', $this->language)
        ]);
        $this->template->layout = 'layouts/admin';
    }


}