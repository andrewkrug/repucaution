<?php
/**
 * User: alkuk
 * Date: 16.04.14
 * Time: 23:49
 */

namespace PlanFeaturesAcl\Feature;

interface AttachedInterface
{
    /**
     * Return feature
     *
     * @return \PlanFeaturesAcl\Feature\FeatureInterface
     */
    public function getFeature();

    /**
     * Return value
     *
     * @return mixed
     */
    public function getValue();


}
