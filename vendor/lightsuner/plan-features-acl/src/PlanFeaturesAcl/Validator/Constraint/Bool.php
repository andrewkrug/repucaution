<?php

namespace PlanFeaturesAcl\Validator\Constraint;

use PlanFeaturesAcl\Validator\Constraint;
use PlanFeaturesAcl\Feature\FeatureInterface;

class Bool extends Constraint
{
    /**
     * {@inheritDoc}
     */
    public function validate(FeatureInterface $feature, $featureValue, $validatedValue)
    {
        return true;
    }
}
