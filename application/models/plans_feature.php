<?php

/**
 * Plans Settings model
 *
 * @author Xedin
 */

use PlanFeaturesAcl\Feature\AttachedInterface;

class Plans_feature extends DataMapper implements AttachedInterface{
    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $table = 'plans_features';

    var $cascade_delete = TRUE;

    var $has_many = array(
        'plan' => array(
            'class' => 'plan',
            'join_table' => 'plans_features'
        ),
        'feature' => array(
            'class' => 'feature',
            'join_table' => 'plans_features'
        )
    );

    /**
     * {@inheritdoc}
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

}

