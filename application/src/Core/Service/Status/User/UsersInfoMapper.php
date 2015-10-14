<?php
/**
 * User: alkuk
 * Date: 27.05.14
 * Time: 17:52
 */

namespace Core\Service\Status\User;

use User;

class UsersInfoMapper
{
    /**
     * @var \Core\Service\Status\User\UserInfo
     */
    protected $activeUserInfo;

    /**
     * @var array
     */
    protected $usersInfoStorage = array();

    /**
     * Set user
     *
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        if (!$user->exists()) {
            throw new \InvalidArgumentException('User should be persisted.');
        }

        if (!isset($this->usersInfoStorage[$user->id])) {
            $this->usersInfoStorage[$user->id] = new UserInfo($user);
        }

        $this->activeUserInfo = $this->usersInfoStorage[$user->id];

        return $this;
    }

    /**
     * @param $method
     * @param array $arguments
     *
     * @return mixed
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function __call($method, array $arguments =  array())
    {
        if (!$this->activeUserInfo) {
            throw new \RuntimeException('UserStatus is not set.');
        }

        if (!method_exists($this->activeUserInfo, $method)) {
            throw new \BadMethodCallException("Undefined method '$method'.");
        }

        return call_user_func_array(array($this->activeUserInfo, $method), $arguments);
    }
}
