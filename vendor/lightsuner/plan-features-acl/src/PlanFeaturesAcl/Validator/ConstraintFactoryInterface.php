<?php
/**
 * User: alkuk
 * Date: 17.04.14
 * Time: 16:58
 */

namespace PlanFeaturesAcl\Validator;

interface ConstraintFactoryInterface
{
    /**
     * Try to get constraint
     *
     * @param string $type
     * @return \PlanFeaturesAcl\Validator\FeatureValidatorInterface
     */
    public function getConstraint($type);
}
