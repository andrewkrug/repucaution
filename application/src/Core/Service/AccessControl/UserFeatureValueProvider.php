<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 16:50
 */

namespace Core\Service\AccessControl;

use User;
use Core\Service\Status\User\UsersInfoMapper;

class UserFeatureValueProvider
{

    /**
     * @var \User
     */
    protected $user;

    /**
     * @var \Core\Service\Status\User\UsersInfoMapper
     */
    protected $userInfoMapper;

    /**
     * @var array
     */
    protected $availableFeatures = array(
        'brand_reputation_monitoring',
        'crm',
        'collaboration_team'

    );

    public function __construct(UsersInfoMapper $userInfoMapper)
    {
        $this->userInfoMapper = $userInfoMapper;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get user's feature value if required
     *
     * @param $feature
     *
     * @return mixed|null
     */
    public function getFeatureValue($feature)
    {
        if (!in_array($feature, $this->availableFeatures)) {
            return null;
        }

        return $this->getCurrentUsersFeatureValue($feature);
    }

    /**
     * Get raw feature value
     *
     * @param $feature
     *
     * @return mixed
     */
    protected function getCurrentUsersFeatureValue($feature)
    {
        $value = null;

        switch ($feature) {
            case 'brand_reputation_monitoring':
                $value = $this->brandReputationMonitoring();
                break;

            case 'crm':
                $value = $this->crm();
                break;

            case 'collaboration_team':
                $value = $this->collaborationTeam();
                break;
        }

        return $value;
    }

    /**
     * Get count of user's mention keyword
     *
     * @return int
     */
    public function brandReputationMonitoring()
    {
        return $this->userInfoMapper->setUser($this->user)->getCurrentMentionKeywordsCount();
    }

    /**
     * Get count of user's crm directories
     *
     * @return int
     */
    public function crm()
    {
        return $this->userInfoMapper->setUser($this->user)->getCurrentCrmDirectoriesCount();
    }

    /**
     * Get count of collaborators
     *
     * @return int
     */
    public function collaborationTeam()
    {
        return $this->userInfoMapper->setUser($this->user)->getCurrentCollaboratorsCount();
    }
}
