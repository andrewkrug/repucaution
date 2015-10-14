<?php

/**
 * Plans Features model
 *
 * @author Xedin
 */
use PlanFeaturesAcl\Feature\FeatureInterface;

class Feature extends DataMapper implements FeatureInterface{
    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;
    var $table = 'features';
    var $has_many = array(
        'plans_feature' => array(
            'class' => 'plans_feature',
            'join_table' => 'plans_features'
        ),
        'plan' => array(
            'class' => 'plan',
            'join_table' => 'plans_features'
        )
    );
    var $validation = array(
        'name' => array(
            'label' => '"Name"',
            'rules' => array('required', 'trim', 'max_length' => 95)
        )
    );

    public function countable() {
        return $this->countable == 1;
    }

    /**
     * Return not plans features
     *
     * @param PlanFeaturesAcl\Feature\FeatureInterface $plansFeatures
     * @return DataMapper
     */
    public function getFreeFeatures($plansFeatures)
    {
        if ($plansFeatures->exists()) {
            $ids = array();
            foreach ($plansFeatures as $plansFeature) {
                $ids[] = $plansFeature->feature_id;
            }
            $this->where_not_in('id', $ids);
        }

        return $this->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationRules()
    {
        return $this->validation_rules;
    }

    /**
     * Check if valid value for this feature
     *
     * @param $value
     * @return mixed
     */
    public function validValue($value)
    {
        $validFunction = 'is_'.$this->type;
        return ($value!='' && $validFunction($value));
    }
}

