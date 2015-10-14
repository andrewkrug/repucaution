<?php
/**
 * User: alkuk
 * Date: 17.04.14
 * Time: 15:02
 */

namespace PlanFeaturesAcl\Validator;

use PlanFeaturesAcl\Feature\FeatureInterface;

interface FeatureValidatorInterface
{

    /**
     * Validate feature
     *
     * @param \PlanFeaturesAcl\Feature\FeatureInterface $feature
     * @param mixed $featureValue
     * @param mixed $validatedValue
     *
     * @return bool
     */
    public function validate(FeatureInterface $feature, $featureValue, $validatedValue);
}
