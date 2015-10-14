<?php
/**
 * Service for getting crm activities
 * 
 * @author ajorjik
 */

namespace Core\Service\Crm;

use Crm_directory;
use Crm_directory_activity;
use Arr;

/**
 * Class Radar
 */
class Manager
{
    /**
     * @var array
     */
    private $filterParams;
    
    /**
     * @var int
     */
    private $limit;
    
    /**
     * @var int
     */
    private $offset;

    /**
     * @var array
     */
    private $socials = array('facebook', 'twitter', 'instagram');
    
    /**
     * @var array
     */
    private $profileUrls = array(
                                'facebook' => 'http://facebook.com/profile.php?id=',
                                'twitter' => 'https://twitter.com/account/redirect_by_id/',
                                'instagram' => 'https://instagram.com/',
                                );
    
    /**
     * @var string
     */
    private $calendarFormat = 'M d, y';
    
    /**
     * @var string
     */
    private $yesterdayFormat = 'h.i a';
    
    /**
     * @var string
     */
    private $otherFormat = 'm.d.Y h.i a';

    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Add filter's params
     *
     * @param $params
     *
     * @return $this
     */
    public function addFilterParams($params)
    {
        foreach ($params as $k=>$v) {
            $this->filterParams[$k] = $v;
        }
        
        return $this;
    }

    /**
     * Get directories activities
     *
     * @param      $userId
     * @param      $profile_id
     * @param null $directoryId
     * @param null $social
     *
     * @return mixed
     */
    public function getDirectoryActivities($userId, $profile_id, $directoryId = null, $social = null)
    {
       $directories = ($directoryId) ?
                      array($directoryId) :
                      $this->getUserDirectories(array(
                          'user_id' => $userId,
                          'profile_id' => $profile_id
                      ))->all_to_array('id');

        if (!$directoryId) {
            $result = array();
            foreach ($directories as $directory) {
                $result[] = $directory['id'];
            }
            $directories = $result;
        }

        $activities = Crm_directory_activity::inst();
        if (count($directories)) {
            $activities->getByDirectories($directories, $this->options['activities_limit'], $this->offset, $social);
        }

        return  $activities;
    }

    /**
     * Get user directories
     *
     * @param      $params
     * @param null $limit
     * @param null $offset
     *
     * @return \DataMapper
     */
    public function getUserDirectories($params, $limit = null, $offset = null)
    {
        $limit = ($limit) ? : $this->options['directories_limit'];

        return Crm_directory::inst()->getUserDirectories($params, $limit, $offset);
    }
    
    /**
     * Get filter's params
     *
     * @return array   
     */
    public function getFilterParams()
    {
        return $this->filterParams;
    }
    
    /**
     * Set limit
     *
     * @param int $limit   
     * @return $this   
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        
        return $this;
    }
    
    /**
     * Set offset
     *
     * @param int $offset
     * @return $this   
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
        
        return $this;
    }
    
    /**
     * Return array of socials
     *
     * @return array   
     */
    public function getSocials()
    {
        return $this->socials;
    }
    
    /**
     * Return date in format: 
     *
     * @param int $date
     * @return object DateTime   
     */
    public function formatRadarDate($date)
    {
        $today = new \DateTime('today');
        $yesterday = new \DateTime('yesterday');
        $fdate = \DateTime::createFromFormat('U', $date);
        if ($today < $fdate) {
            $result = $fdate->format($this->calendarFormat);
        } elseif ($today > $fdate && $fdate > $yesterday ){
            $result = 'yesterday on '.$fdate->format($this->yesterdayFormat);
        } else {
            $result = $fdate->format($this->otherFormat);
        }
        
        return $result;
    }
   
    /**
     * Get profile url
     *
     * @param string $social
     * @return string | null   
     */
    public function getProfileUrl($social)
    {
        return Arr::get($this->profileUrls, $social, null);
    }

    /**
     * Get exists activities socials
     *
     * @param $directoryId
     * @return array
     */
    public function getExistsActivitiesSocials($directoryId)
    {
        $result = array();
        foreach ($this->socials as $k => $v) {
            $result[$v] = Crm_directory::inst($directoryId)->hasSocialActivities($v);
        }

        return $result;
    }
       
}