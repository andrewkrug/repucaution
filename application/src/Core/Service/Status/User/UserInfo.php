<?php
/**
 * User: alkuk
 * Date: 27.05.14
 * Time: 17:52
 */

namespace Core\Service\Status\User;

use User;

class UserInfo
{
    /**
     * @var \User
     */
    protected $user;

    /**
     * @var array
     */
    protected $properties = array();

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get current user's mention keywords count
     *
     * @return int
     */
    public function getCurrentMentionKeywordsCount()
    {
        if (!isset($this->properties[__METHOD__])) {
            $this->properties[__METHOD__] = $this->user->getMentionKeywordsCount();
        }

        return $this->properties[__METHOD__];
    }

    /**
     * Get current user's crm directories count
     *
     * @return int
     */
    public function getCurrentCrmDirectoriesCount()
    {
        if (!isset($this->properties[__METHOD__])) {
            $this->properties[__METHOD__] = $this->user->getCrmDirectoriesCount();
        }

        return $this->properties[__METHOD__];
    }


    /**
     * Get current collaborators count
     *
     * @return int
     */
    public function getCurrentCollaboratorsCount()
    {
        if (!isset($this->properties[__METHOD__])) {
            $this->properties[__METHOD__] = $this->user->getCollaboratorsCount();
        }

        return $this->properties[__METHOD__];
    }
}
