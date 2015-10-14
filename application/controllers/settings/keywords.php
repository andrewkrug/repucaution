<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Keywords extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('keywords', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('keywords', $this->language)
        ]);
        $this->template->set('section', 'keywords');
    }

    /**
     * Form with keywords list
     */
    public function index() {

        $this->load->config('site_config', TRUE);
        $keywords_config = $this->config->item('keywords', 'site_config');
        $keywords_count = isset($keywords_config['count']) && $keywords_config['count'] ? $keywords_config['count'] : 10;

        // get user additional info (address)
        $user_additional = User_additional::inst()->get_by_user_and_profile($this->c_user->id, $this->profile->id);
        // get available keywords
        $keywords = Keyword::inst()->get_user_keywords($this->c_user->id, $this->profile->id);

        if ($this->input->post()) {
            // validate posted address name
            $new_address_name = $this->input->post('address');

            $adress_error_string = User_additional::validate_address($this->input->post());            

            // validate posted keywords
            $new_keywords_names = array_slice($this->input->post('keywords'), 0, $keywords_count);

            $keywords_errors = Keyword::validate_keywords($new_keywords_names);

            // chek for errors
            if (empty($adress_error_string) && empty($keywords_errors)) {

                $user_additional = $user_additional->update_address(
                    $this->input->post(),
                    $this->c_user->id,
                    $this->profile->id
                );

                $keywords = $keywords->update_keywords(
                    $new_keywords_names,
                    $this->c_user->id,
                    $this->profile->id
                );

                $this->addFlash(lang('keywords_saved_success'), 'success');

            } else {
                $address_name = $new_address_name;
                $keywords_names = array_slice(
                    array_values($this->input->post('keywords')),
                    0,
                    $keywords_count
                );

                $errors = array(
                    'keywords' => $keywords_errors,
                    'address' => $adress_error_string,
                );
                $this->addFlash($errors);
            }
            redirect('settings/keywords');
        }

        // escape keywords names and website name
        $address_name = isset($address_name)
            ? HTML::chars($address_name)
            : HTML::chars($user_additional->address);
        
        $keywords_names = isset($keywords_names) 
            ? HTML::chars_arr($keywords_names)
            : HTML::chars_arr(array_values($keywords->all_to_single_array('keyword')));   

        JsSettings::instance()->add(array(
            'autocomplete_url' => site_url('settings/keywords/google_autocomplete'),
        ));

        CssJs::getInst()->c_js();

        $this->template->set('address_id', $user_additional->address_id);
        $this->template->set('address_name', $address_name);
        $this->template->set('keywords_names', $keywords_names);
        $this->template->set('keywords_count', $keywords_count);
        $this->template->render();
    }


    /**
     * Autocomplete for company address
     */
    public function google_autocomplete(){
        $term = $this->input->get('term');
        $term = trim($term);

        try {

            $developer_key = Api_key::value('google', 'developer_key');

            $gls = $this->load->library('gls');
            $gls->set(array(
                'key' => $developer_key,
            ));

            $rows = $gls->autocomplete($term);

            if ( ! $rows OR ! isset($rows['predictions'])) {
                throw new Exception;
            }

            $rows = $rows['predictions'];

            $data = array();
            foreach($rows as $row) {
                $data[] = array(
                    'id' => $row['id'],
                    'label' => $row['description'],
                );
            }

        } catch (Exception $e) {
            exit;
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

    }

}