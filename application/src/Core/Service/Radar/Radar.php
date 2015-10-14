<?php
/**
 * Service for getting mentions 
 * 
 * @author ajorjik
 */

namespace Core\Service\Radar;

use User;
use Mention;
use DateTime;
use Arr;
use Socializer;

/**
 * Class Radar
 */
class Radar
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
    private $socials = array('facebook', 'twitter', 'google', 'instagram');
    
    /**
     * @var array
     */
    private $profileUrls = array(
                                'facebook' => 'http://facebook.com/profile.php?id=',
                                'twitter' => 'https://twitter.com/account/redirect_by_id/',
                                'google' => 'https://plus.google.com/',
                                'instagram' => 'https://instagram.com/',
                                );
    
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
    private $fieldInfluencersOption = array(
        'twitter_followers' => 'followers_count',
        'facebook_friends' => 'friends_count',
        'google+_people' => 'people_count',
        'twitter_tweet_retweets' => 'retweet_count',
        'facebook_post_likes' => 'likes_count',
        'facebook_post_comments' => 'comments_count',
        'google+_post_likes' => 'plusoners_count',
        'google+_post_shares' => 'resharers_count',
        'google+_post_comments' => 'comments_count',
        'instagram_likes_count' => 'instagram_likes',
        'instagram_comments_count' => 'instagram_comments'
    );

    public function __construct() {
        $this->yesterdayFormat = lang('time_format');
        $this->otherFormat = lang('mentions_format');
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
            if ($k == 'from') {
                $k = 'created_at >=';
            }
            if ($k == 'to') {
                $k = 'created_at <=';
            }
            $this->filterParams[$k] = $v;
        }
        
        return $this;
    }
    
    /**
     * Return filtered mentions 
     *
     * @return object Mention  
     */
    public function getRadarMentions()
    {
       return  Mention::inst()->getByFilters($this->getFilterParams(), $this->limit, $this->offset); 
    }

    /**
     * Return filtered mentions
     *
     * @param $userId
     *
     * @return object Mention
     */
    public function getRadarInfluencers($userId)
    {

        $conditions = new \Influencers_condition();
        $conditions->get();
        $cou = $conditions->count();

        $blacklist = new \Influencers_blacklist();
        $whitelist = new \Influencers_whitelist();
        $blackIds = $blacklist->getByUser($userId);
        $whiteIds = $whitelist->getByUser($userId);
        $result = Mention::inst();
        if (!empty($blackIds)) {
            $result->where_not_in('creator_id', $blackIds);
        }
        $i=0;
        $result->group_start();
        foreach ($this->socials as $social) {

            $relatedModel = 'mention_'.$social;
            $result->include_related($relatedModel);

            foreach ($conditions as $c) {
                $option = $c->option;
                $arC = explode('_', $option);
                if ($social == 'google') {
                    $social = 'google+';
                }

                if ($social == $arC[0]) {
                    if ($i == 0) {
                        $result->where_related($relatedModel,
                            $this->fieldInfluencersOption[$option].' >=',
                            (int) $c->value);
                    } else {
                        $result->or_where_related($relatedModel,
                            $this->fieldInfluencersOption[$option].' >=',
                            (int) $c->value);
                    }
                    $i++;
                    if ($i == $cou) {
                        if (!empty($whiteIds)) {
                            $result->or_where_in('creator_id', array_keys($whiteIds));
                        }
                        $result->group_end();
                    }
                }

            }

        }

        $result->getByFilters($this->getFilterParams(), $this->limit, $this->offset);
        return $result;
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
        $today = new DateTime('today');
        $yesterday = new DateTime('yesterday');
        $tomorrow = new DateTime('tomorrow');
        $fdate = DateTime::createFromFormat('U', $date);
        if ($today < $fdate && $tomorrow > $fdate) {
            $result = 'today on '.$fdate->format($this->yesterdayFormat);
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
     * Follow mention creator
     *
     * @param $mention
     * @param array $access_token
     * @return mixed
     */
    public function twitterMentionFollow($mention, $access_token)
    {
        $twitter = Socializer::factory('twitter', $mention->user_id, $access_token);
        $result = $twitter->follow($mention->creator_id);
        if ($result) {
            $mention->setOtherField('following', true);
        }

        return $result;
    }
       
}