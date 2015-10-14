<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 13:59
 */

namespace Core\Service\AccessControl;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use User;
use Core\Service\AccessControl\RoleAccessControl;
use Core\Service\AccessControl\PlanAccessControl;

class AppAccessControl
{
    /**
     * @var \User
     */
    protected $user;

    /**
     * @var \Core\Service\AccessControl\RoleAccessControl
     */
    protected $roleAccessControl;

    /**
     * @var \Core\Service\AccessControl\PlanAccessControl
     */
    protected $planAccessControl;


    public function __construct(
        RoleAccessControl $roleAccessControl,
        PlanAccessControl $planAccessControl,
        User $user = null
    ) {
        $this->user = $user;
        $this->roleAccessControl = $roleAccessControl;
        $this->planAccessControl = $planAccessControl;
    }


    public function setUser(User $user = null)
    {
        $this->user = $user;
    }

    public  function getUser()
    {
        if (get_instance()->ion_auth->is_collaborator()) {
            $groupName = get_instance()->config->item('default_group', 'ion_auth');
            $group = get_instance()->ion_auth_model->getGroupByName($groupName);
            $user = new User(get_instance()->ion_auth->get_user_id());

            return $user->getMajorUserInGroup($group->id);

        } else {
            return $this->user;
        }
    }

    /**
     * Check role ability
     *
     * @param string $attributes
     *
     * @return bool
     */
    public function isGrantedRole($attributes)
    {
        if (!$this->getUser()) {
            return false;
        }

        return $this->roleAccessControl->isGranted($attributes, $this->getUser());
    }

    /**
     * Check plan ability
     *
     * @param string $attributes
     *
     * @return bool
     */
    public function isGrantedPlan($attributes)
    {
        if (!$this->getUser()) {
            return false;
        }

        return $this->planAccessControl->isGranted($attributes, $this->getUser());
    }

    /**
     * Check plan abilities
     *
     * @param string $attributes
     *
     * @return bool
     */
    public function isGrantedPlanOr(array $attributes)
    {
        if (!$this->getUser()) {
            return false;
        }

        return $this->planAccessControl->isGrantedOr($attributes, $this->getUser());
    }

    /**
     * Check for feature in plan
     *
     * @param string $feature
     *
     * @return bool
     */
    public function planHasFeature($feature)
    {
        if (!$this->getUser()) {
            return false;
        }

        return $this->planAccessControl->hasFeature($feature, $this->getUser());
    }

    /**
     * Throw Plan Access Denied Exception
     *
     * @throws \Core\Exception\PlanAccessDeniedException
     */
    public function throwPlanAccessDeniedException()
    {
        throw $this->planAccessControl->createPlanAccessDeniedException();
    }

    /**
     * Throw an exception if plan has not this feature
     *
     * @param string $feature
     *
     * @throws \Core\Exception\PlanAccessDeniedException
     */
    public function planHasFeatureWithException($feature)
    {
        $this->checkUser();
        $this->planAccessControl->hasFeatureWithException($feature, $this->getUser());
    }

    /**
     * Check role ability with AccessDeniedException
     *
     * @param string $attributes
     *
     * @throw \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function isGrantedRoleWithException($attributes)
    {
        $this->checkUser();
        $this->roleAccessControl->isGrantedWithException($attributes, $this->getUser());
    }

    /**
     * Check plan abilities with PlanAccessDeniedException
     *
     * @param array $attributes
     *
     * @throw \Core\Exception\PlanAccessDeniedException
     */
    public function isGrantedPlanWithExceptionOr(array $attributes)
    {
        $this->checkUser();
        $this->planAccessControl->isGrantedWithExceptionOr($attributes, $this->getUser());
    }

    /**
     * Check plan ability with PlanAccessDeniedException
     *
     * @param string $attributes
     *
     * @throw \Core\Exception\PlanAccessDeniedException
     */
    public function isGrantedPlanWithException($attributes, $object = null)
    {
        $this->checkUser();
        $this->planAccessControl->isGrantedWithException($attributes, $this->getUser());
    }

    public function getPlanFeatureValue($featureName)
    {
        if (!$this->getUser()) {
            return null;
        }

        return $this->planAccessControl->getFeatureValue($featureName, $this->getUser());
    }

    /**
     * If user not passed - throw exception
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    protected function checkUser()
    {
        if (!$this->getUser()) {
            throw new AccessDeniedHttpException();
        }
    }
}
