<?php

/**
 * Plans model
 *
 * @author Xedin
 */

use PlanFeaturesAcl\Plan\PlanInterface;

class Plan extends DataMapper implements PlanInterface{
    var $auto_populate_has_one = TRUE;
    var $auto_populate_has_many = TRUE;

    var $table = 'plans';

    var $has_many = array(
        'subscription',
        'feature' => array(
            'class' => 'feature',
            'join_table' => 'plans_features'
        ),
        'plans_feature' => array(
            'class' => 'plans_feature',
            'join_table' => 'plans_features'
        ),
        'plans_period' => array(
            'class' => 'plans_period',
            'join_table' => 'plans_period'
        )
    );
    
    var $validation = array(
        'name' => array(
            'label' => 'Name',
            'rules' => array('trim', 'max_length' => 255, 'required', 'unique'),
        )

    );

    public function get_basic_plans_settings () {
        $ids  = array();
        $plans = $this->get();
        foreach ($plans as $_plan) {
            $plans_setting = new Plans_setting();
            $plans_setting->where('plan_id', $_plan->id)->order_by('cost', 'ASC')->limit(1)->get();
            array_push($ids, $plans_setting->id);
        }
        return $ids;
    }

    /**
     * Used then user upgrade plan. Select plans higher then current plan
     *
     * @access public
     * @param $plan_id
     * @return DataMapper
     */
    public function get_for_upgrade($plan_id) {
        return $this->where('id > ', $plan_id)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getAttachedFeatures()
    {
        return $this->plans_feature->order_by('weight', 'ASC')->get();
    }

    /**
     * Remove not actual periods of plan
     *
     * @param mixed $savePeriodsIds ids of actual plan`s periods
     */
    public function deleteOldPlanPeriods($savePeriodsIds = null)
    {
        $result = $this->plans_period;
        $periods = $result->where_not_in('id', $savePeriodsIds)->get();
        $this->deleteStripePlans($periods);
        if ($savePeriodsIds) {
            $result->where_not_in('id', $savePeriodsIds);
        }

        return $result->get()->delete_all();
    }

    /**
     * Remove not actual features of plan
     *
     * @param mixed $saveFeaturesIds ids of actual plan`s features
     */
    public function deleteOldPlanFeatures($saveFeaturesIds = null)
    {
        $result = $this->plans_feature;
        if ($saveFeaturesIds) {
            $result->where_not_in('id', $saveFeaturesIds);
        }

        return $result->get()->delete_all();
    }

    /**
     * Get actual plans
     *
     * @param bool $withTrial
     * @param bool $withSpecial
     * @return DataMapper
     */
    public function getActualPlans($withTrial = false, $withSpecial = false)
    {
        $plans = $this->where('deleted', false);
        if (!$withTrial) {
            $plans->where('trial', false);
        }
        if (!$withSpecial) {
            $plans->where('special', false);
        }

        return $plans->order_by('weight', 'ASC')->get();
    }

    /**
     * Get actual plans
     *
     * @param User $user
     * @return DataMapper
     */
    public function getActualPlansWithoutActive($user)
    {
        $plans = $this->where('deleted', false);
        $plans->where('trial', false);
        $plans->where('special', false);

        if ($user->stripe_id) {
            $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
            \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
            /* @var /Stripe/Subscription[] $subscriptions */
            $subscriptions = \Stripe\Customer::retrieve($user->stripe_id)->subscriptions->all();
            $subscriptionsStripeIds = array();
            foreach($subscriptions->data as $subscription) {
                $subscriptionsStripeIds[] = $subscription->id;
            }
            if (!empty($subscriptionsStripeIds)) {
                $payment_transactions = $user->payment_transaction
                    ->where_in('payment_id', $subscriptionsStripeIds)
                    ->get();
                $planIds = array();
                foreach ($payment_transactions as $payment_transaction) {
                    $planIds[] = $payment_transaction->subscription->get()->plan_id;
                }
                if (!empty($planIds)) {
                    $plans->where_not_in('id', $planIds);
                }
            }
        }

        return $plans->order_by('weight', 'ASC')->get();
    }

    /**
     * Get periods of plan
     *
     * @return DataMapper
     */
    public function getPeriods()
    {
        return $this->plans_period->order_by('price', 'ASC')->get();
    }


    /**
     * Get month heriod of plan
     *
     * @return DataMapper
     */
    public function getTrialPeriod()
    {
        return $this->plans_period->order_by('period', 'ASC')->get(1);
    }

    /**
     * Is trial
     *
     * @return boolean
     */
    public function isTrial()
    {
        return (bool)$this->trial;
    }

    /**
     * Create plans in Stripe.
     */
    public function createStripePlans() {
        $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
        \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
        foreach ($this->getPeriods() as $period) {
            try {
                \Stripe\Plan::create(array(
                        'amount' => $period->price,
                        'interval' => "month",
                        'name' => $this->name . ' (' . $period->period . ' months)',
                        'currency' => "usd",
                        'id' => $period->id,
                        'interval_count' => $period->period
                    )
                );
            } catch(Exception $e) {
                try {
                    $plan = \Stripe\Plan::retrieve($period->id);
                    $plan->name = $this->name . ' (' . $period->period . ' months)';
                    $plan->save();
                } catch(Exception $e) {}
            }

        }
    }

    /**
     * @param null|array $periods
     */
    public function deleteStripePlans($periods = null) {
        $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
        \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
        foreach($periods as $period) {
            try {
                $plan = \Stripe\Plan::retrieve($period->id);
                $plan->delete();
            } catch(Exception $e) {}
        }
    }
}

