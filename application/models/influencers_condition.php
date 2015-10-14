<?php
/**
 * User: alkuk
 * Date: 11.03.14
 * Time: 16:10
 */

class Influencers_condition extends DataMapper
{
    var $table = 'influencers_conditions';

    var $validation = array(
        'option' => array(
            'label' => 'Option',
            'rules' => array('trim', 'unique', 'required'),
        ),
        'option_name' => array(
            'label' => 'Option name',
            'rules' => array('trim', 'required'),
        ),
        'value' => array(
            'label' => 'Value',
            'rules' => array('trim', 'numeric'),
        ),
    );

    /**
     * All options to array
     *
     * @return array
     */
    public static function allToOptionsArray()
    {
        $object = new self();
        $conditions = $object->get();
        $optionsArray = array();
        foreach ($conditions as $condition) {
            $optionsArray[$condition->option] = $condition->value;
        }

        return $optionsArray;
    }
}
 