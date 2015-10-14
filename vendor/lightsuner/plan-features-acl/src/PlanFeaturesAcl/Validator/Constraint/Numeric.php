<?php

namespace PlanFeaturesAcl\Validator\Constraint;

use PlanFeaturesAcl\Feature\FeatureInterface;
use PlanFeaturesAcl\Validator\Constraint;
use InvalidArgumentException;

class Numeric extends Constraint
{

    /**
     * {@inheritDoc}
     */
    public function validate(FeatureInterface $feature, $featureValue, $validatedValue)
    {
        if (!is_numeric($featureValue)) {
            throw new InvalidArgumentException('The value of feature should be numeric(is_numeric).');
        }

        if (!is_numeric($validatedValue)) {
            throw new InvalidArgumentException('The validated value should be numeric(is_numeric).');
        }

        $rules = $this->parseValidationRules($feature->getValidationRules());

        if (empty($rules)) {
            return true;
        }

        $isValid = true;

        foreach ($rules as $rule) {
            switch ($rule) {
                case 'lt':
                    $isValid = ($validatedValue < $featureValue);
                    break;
                case 'lte':
                    $isValid = ($validatedValue <= $featureValue);
                    break;
                case 'gt':
                    $isValid = ($validatedValue > $featureValue);
                    break;
                case 'gte':
                    $isValid = ($validatedValue >= $featureValue);
                    break;
                case 'eq':
                    $isValid = ($validatedValue == $featureValue);
                    break;
                case 'neq':
                    $isValid = ($validatedValue != $featureValue);
                    break;
            }
            if (!$isValid) {
                break;
            }
        }

        return $isValid;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableRules()
    {
        return array(
            'lt', //equal
            'lte', //less than or equal
            'gt', //greater than
            'gte', //greater than or equal
            'eq', //equal
            'neq' //not equal
        );
    }
}
