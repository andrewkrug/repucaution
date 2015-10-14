<?php
/**
 * User: alkuk
 * Date: 18.04.14
 * Time: 8:59
 */

namespace PlanFeaturesAcl\Validator;

abstract class Constraint implements FeatureValidatorInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAvailableRules()
    {
        return array();
    }

    /**
     * Fetch rules from json
     *
     * @param null|string $rules
     *
     * @return array
     */
    protected function parseValidationRules($rules)
    {
        $parsedRules = array();

        if (!$rules || !is_string($rules)) {
            return $parsedRules;
        }

        $rulesArray = @json_decode($rules, true);

        if (null === $rulesArray || !is_array($rulesArray)) {
            return $parsedRules;
        }

        $availableRules = $this->getAvailableRules();

        foreach ($rulesArray as $rule => $options) {
            if (!in_array($rule, $availableRules)) {
                continue;
            }
            $parsedRules[$rule] = $options;
        }

        return $parsedRules;
    }
}
