<?php
/**
 * User: alkuk
 * Date: 17.04.14
 * Time: 0:08
 */

namespace PlanFeaturesAcl\Plan;

interface PlanInterface
{
    /**
     * Return array of attached features
     *
     * @return \PlanFeaturesAcl\Feature\AttachedInterface[]
     */
    public function getAttachedFeatures();

}
