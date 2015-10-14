<?php
/**
 * User: alkuk
 * Date: 16.04.14
 * Time: 23:29
 */

namespace PlanFeaturesAcl\Feature;

interface FeatureInterface
{
    /**
     * Return slug
     *
     * @return string
     */
    public function getSlug();

    /**
     * Return Name
     *
     * @return string
     */
    public function getName();

    /**
     * Return description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Return type
     *
     * @return string
     */
    public function getType();

    /**
     * Return validation rules
     *
     * @return array
     */
    public function getValidationRules();

}
