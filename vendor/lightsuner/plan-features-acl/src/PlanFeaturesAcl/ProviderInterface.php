<?php
/**
 * User: alkuk
 * Date: 17.04.14
 * Time: 16:01
 */
namespace PlanFeaturesAcl;

use PlanFeaturesAcl\Plan\PlanInterface;

interface ProviderInterface
{
    /**
     * Check is enabled
     *
     * @return bool
     */
    public function isEnable();

    /**
     * Disable
     *
     * @return $this
     */
    public function disable();

    /**
     * Enable service
     *
     * @return $this
     */
    public function enable();

    /**
     * Check is plan grant access to feature
     *
     * @param string $feature
     * @param mixed|null $args
     *
     * @return bool
     */
    public function isGranted($featureName, $validatedValue = null);

    /**
     * Set Plan
     *
     * @param PlanInterface $plan
     */
    public function setPlan(PlanInterface $plan = null);

    /**
     * Check for feature in plan
     *
     * @param string $feature
     *
     * @return bool
     */
    public function hasFeature($featureName);

    /**
     * Get value of attached feature
     *
     * @param $featureName
     *
     * @return mixed
     */
    public function getFeatureValue($featureName);
}
