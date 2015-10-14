<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
    extend input class because of bug that comes up using CLI
    http://ellislab.com/forums/viewthread/227672/
*/

class MY_Input extends CI_Input{

    public function __construct() {
        parent::__construct();
    }

    /**
    * Fetch the IP Address
    *
    * @return   string
    */
    public function ip_address()
    {
        if ($this->ip_address !== FALSE)
        {
            return $this->ip_address;
        }

        $proxy_ips = config_item('proxy_ips');
        if ( ! empty($proxy_ips))
        {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header)
            {
                if (($spoof = $this->server($header)) !== FALSE)
                {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    if (strpos($spoof, ',') !== FALSE)
                    {
                        $spoof = explode(',', $spoof, 2);
                        $spoof = $spoof[0];
                    }

                    if ( ! $this->valid_ip($spoof))
                    {
                        $spoof = FALSE;
                    }
                    else
                    {
                        break;
                    }
                }
            }

            $this->ip_address = ($spoof !== FALSE && in_array($_SERVER['REMOTE_ADDR'], $proxy_ips, TRUE))
                ? $spoof : $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            if(isset($_SERVER['REMOTE_ADDR'])) {
                $this->ip_address = $_SERVER['REMOTE_ADDR'];
            } else {
                $this->ip_address = '0.0.0.0'; 
            }
        }

        if ( ! $this->valid_ip($this->ip_address))
        {
            $this->ip_address = '0.0.0.0';
        }

        return $this->ip_address;
    }

}

/* End of file MY_Input.php */
/* Location: ./application/core/Input.php */