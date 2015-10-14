<?php

/**
 * Plans Settings model
 *
 * @author Xedin
 */
class Plans_period extends DataMapper {
    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $table = 'plans_period';

    var $has_one = array(
        'plan' => array(
            'class' => 'plan',
            'join_table' => 'plans'
        )
    );

    var $cascade_delete = TRUE;

    var $validation = array(
        'period' => array(
            'label' => 'Period',
            'rules' => array('trim', 'max_length' => 5, 'required', 'period'),
        ),
        'price' => array(
            'label' => 'Price',
            'rules' => array('trim', 'max_length' => 10, 'required', 'price'),
        ),
    );

    /**
     * Validation rule period
     *
     * @param $field
     * @return bool|string
     */
    public function _period($field)
    {
        $pattern = '/[^0-9]/';
        preg_match($pattern, $this->{$field}, $match);
        if (is_numeric($this->{$field}) && empty($match) && $this->{$field}>0) {
            return true;
        }

        return ucfirst($field)." value is not a valid integer numeric";

    }

    /**
     * Validation rule price
     *
     * @param $field
     * @return bool|string
     */
    public function _price($field)
    {
        $trial = !empty($_POST['trial']);
        $pattern = '/^\d*(\.\d{1,2})?/';
        preg_match($pattern, $this->{$field}, $match);
        if (is_numeric($this->{$field})  && strlen($match[0]) == strlen($this->{$field})) {
            if ($trial || $this->{$field}>0) {
                $newVal = intval($match[0]*100);
                $this->{$field} = $newVal;

                $transactionOptions = get_instance()->container->param('parameters.payment.transaction.options');
                if (!$trial && isset($transactionOptions['minimal_amount']) && $newVal < $transactionOptions['minimal_amount']) {
                    $formatedMinimalAmount = number_format(floatval($transactionOptions['minimal_amount']/100), 2);
                    return sprintf("%s value should not be less than %s.", ucfirst($field), $formatedMinimalAmount);
                }

                return true;
            }
        }

        return ucfirst($field)." value is not a valid price. Must contain no more than two digits after delimeter.";
    }

    /**
     * Get price for view
     *
     * @return float
     */
    public function viewPrice()
    {
        return number_format($this->price/100, 2);
    }




}

