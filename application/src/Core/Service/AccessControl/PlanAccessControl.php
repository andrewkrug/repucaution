<?php
/**
 * User: alkuk
 * Date: 23.05.14
 * Time: 16:05
 */

namespace Core\Service\AccessControl;

use Core\Exception\PlanAccessDeniedException;
use InvalidArgumentException;
use PlanFeaturesAcl\ProviderInterface;
use User;

class PlanAccessControl implements AccessControlInterface
{
    /**
     * @var \PlanFeaturesAcl\ProviderInterface
     */
    protected $planFeaturesAclProvider;

    /**
     * @var \Core\Service\AccessControl\UserFeatureValueProvider
     */
    protected $userFeatureValueProvider;

    /**
     * @var array
     */
    protected $planCache = array();

    public function __construct(
        ProviderInterface $planFeaturesAclProvider,
        UserFeatureValueProvider $userFeatureValueProvider
    ) {
        $this->planFeaturesAclProvider = $planFeaturesAclProvider;
        $this->userFeatureValueProvider = $userFeatureValueProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function isGranted($attributes, $object = null)
    {
        $this->checkObjectType($object);

        $this->planFeaturesAclProvider->setPlan($this->getPlanFromCache($object));

        $this->userFeatureValueProvider->setUser($object);
        $validatedValue = $this->userFeatureValueProvider->getFeatureValue($attributes);

        return $this->planFeaturesAclProvider->isGranted($attributes, $validatedValue);
    }

    /**
     * Return true if one of many attributes pass
     *
     * @param array $attributes
     * @param User | null $object
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function isGrantedOr(array $attributes, $object = null)
    {
        $this->checkObjectType($object);

        foreach ($attributes as $attribute) {
            if ($this->isGranted($attribute, $object)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isGrantedWithException($attributes, $object = null)
    {
        if (!$this->isGranted($attributes, $object)) {
            redirect('subsctipt/plans');
//            throw $this->createPlanAccessDeniedException();
        }
    }

    /**
     * Throw an exception if none of attributes not pass
     *
     * @param array $attributes
     * @param User | null $object
     *
     * @throws \Core\Exception\PlanAccessDeniedException
     */
    public function isGrantedWithExceptionOR(array $attributes, $object = null)
    {
        if (!$this->isGrantedOr($attributes, $object)) {
            throw $this->createPlanAccessDeniedException();
        }
    }

    /**
     * Create Plan Access Denied Exception
     *
     * @return \Core\Exception\PlanAccessDeniedException
     */
    public function createPlanAccessDeniedException()
    {
        return new PlanAccessDeniedException();
    }


    /**
     * Check for feature in plan
     *
     * @param string $attributes
     * @param User | null $object
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function hasFeature($attributes, $object = null)
    {
        $this->checkObjectType($object);
        $this->planFeaturesAclProvider->setPlan($this->getPlanFromCache($object));

        return $this->planFeaturesAclProvider->hasFeature($attributes, $object);
    }

    /**
     * Throw an exception if plan has not this feature
     *
     * @param string $attributes
     * @param User | null $object
     *
     * @throws \Core\Exception\PlanAccessDeniedException
     * @throws \InvalidArgumentException
     */
    public function hasFeatureWithException($attributes, $object = null)
    {
        $this->checkObjectType($object);

        if (!$this->hasFeature($attributes, $object)) {
            throw $this->createPlanAccessDeniedException();
        }
    }

    /**
     *
     *
     * @param $featureName
     * @param User | null $object
     *
     * @return mixed
     */
    public function getFeatureValue($featureName, $object = null)
    {
        $this->checkObjectType($object);
        if (!$this->hasFeature($featureName, $object)) {
            return null;
        }

        return $this->planFeaturesAclProvider->getFeatureValue($featureName);
    }

    /**
     * Get active user plan
     *
     * @param User $user
     *
     * @return null | \PlanFeaturesAcl\Plan\PlanInterface
     */
    protected function getPlanFromCache(User $user)
    {
        if (!isset($this->planCache[$user->id])) {
            $this->planCache[$user->id] = $user->getActivePlan();
        }

        return $this->planCache[$user->id];
    }

    /**
     * Throw error if object is not user
     *
     * @param $object
     *
     * @throws \InvalidArgumentException
     */
    protected function checkObjectType($object)
    {
        if (!$object || !($object instanceof User)) {
            throw new InvalidArgumentException();
        }
    }
}
