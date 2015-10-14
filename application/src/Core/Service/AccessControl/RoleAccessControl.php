<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 14:23
 */

namespace Core\Service\AccessControl;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use InvalidArgumentException;
use User;

class RoleAccessControl implements AccessControlInterface
{
    /**
     * @var array
     */
    protected $abilities;

    public function __construct(array $abilities)
    {
        $this->abilities = $abilities;
    }

    /**
     * Check access
     *
     * @param string $attributes
     * @param mixed $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null)
    {

        if (!$object || !($object instanceof User)) {
            throw new InvalidArgumentException();
        }

        $roles = $object->getRoles();

        switch (true) {
            case in_array('superadmin', $roles):
                if ($this->checkAbility('superadmin', $attributes)) {
                    return true;
                }
            case in_array('admin', $roles):
                if ($this->checkAbility('admin', $attributes)) {
                    return true;
                }
                break;
            case in_array('managers', $roles):
                if ($this->checkAbility('managers', $attributes)) {
                    return true;
                }
                break;
            case in_array('members', $roles):
                if ($this->checkAbility('members', $attributes)) {
                    return true;
                }
                break;

        }

        return false;

    }

    /**
     * {@inheritdoc}
     */
    public function isGrantedWithException($attributes, $object = null)
    {
        if (!$this->isGranted($attributes, $object)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Check ability in certain section
     *
     * @param string $section
     * @param string $ability
     *
     * @return bool
     */
    protected function checkAbility($section, $ability)
    {
        return (isset($this->abilities[$section]) &&
            is_array($this->abilities[$section]) &&
            in_array($ability, $this->abilities[$section])
        );
    }

}
