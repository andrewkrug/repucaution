<?php
/**
 * Service for working with Mailchimp functionality
 *
 * @author ajorjik@tut.by
 */

namespace Core\Service\Mailchimp;

use User;
use Api_key;
use Drewm\MailChimp;
use \Exception;

class Manager
{
    /**
     * @var MailChimp
     */
    protected $mailchimp;

    /**
     * @var string
     */
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = Api_key::inst()->get_by_social('mailchimp')->value;
        $this->mailchimp = new MailChimp($this->apiKey);
    }

    /**
     * Subscribe users to mailchimp`s list
     *
     * @param array $groups
     * @param array $lists
     * @return array
     */
    public function exportEmails($groups, $lists)
    {
        $user = new User();
        $emails = $user->getEmailsByGroups($groups);

        if (!count($emails)) {
            throw new Exception("You have not any users in selected groups!");
        }

        $batch = array();
        foreach ($emails as $k => $v) {
            $batch[] = array('email' => $v);
        }

        $params = array(
            'batch' => $batch,
            'double_optin'      => false,
            'update_existing'   => true,
            'replace_interests' => false,
        );

        $result = array();
        foreach ($lists as $id => $name) {
            $params['id'] = $id;
            $response = $this->mailchimp->call('lists/batch-subscribe', $params);
            $result[] = $name.": added - ".$response['add_count'].
                              ", updated - ".$response['update_count'].
                              ", errors - ".$response['error_count'];

        }

        return $result;
    }

    /**
     * Get lists from mailchimp account
     *
     * @return mixed
     */
    public function getLists()
    {
        $params = array(
            'filters' => array(),
            'start' => 0,
            'limit' => 25,
            'sort_field' => 'created',
            'sort_dir' => 'DESC',
        );
        return $this->mailchimp->call('lists/list', $params);
    }

}