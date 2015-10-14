<?php

namespace PlanFeaturesAcl\Validator;

use PlanFeaturesAcl\Feature\FeatureInterface;
use PlanFeaturesAcl\Validator\ConstraintFactoryInterface;

class FeatureValidator implements FeatureValidatorInterface
{
    /**
     * @var \PlanFeaturesAcl\Validator\ConstraintFactoryInterface
     */
    protected $constraintFactory;

    public function __construct(ConstraintFactoryInterface $constraintFactory)
    {
        $this->constraintFactory = $constraintFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function validate(FeatureInterface $feature, $featureValue, $validatedValue)
    {

        $constraint = $this->constraintFactory->getConstraint($feature->getType());

        return $constraint->validate($feature, $featureValue, $validatedValue);
    }
}
