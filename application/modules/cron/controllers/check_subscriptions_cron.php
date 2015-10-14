<?php

class Check_subscriptions_cron extends CLI_controller {

    /**
     * Check stripe data.
     *
     * @access public
     * @return void
     */
    public function run() {
        $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
        if ($paymentGateway->exists()) {
            \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
            $subscriptions = new Subscription();

            $allSubscriptions = $subscriptions
                ->where('status', Subscription::STATUS_ACTIVE)
                ->get();

            /* @var Subscription $_subscription */
            foreach($allSubscriptions as $_subscription) {

                $end = DateTime::createFromFormat('Y-m-d', $_subscription->end_date);
                if( $end->getTimestamp() > strtotime('now') ) {

                    $paymentTransaction = $_subscription->payment_transaction->get();
                    if ($paymentTransaction->system == 'stripe') {
                        $user = new User($_subscription->user_id);
                        try {
                            $customer = \Stripe\Customer::retrieve($user->stripe_id);
                            $subscription = $customer->subscriptions->retrieve($paymentTransaction->payment_id);
                        } catch (Exception $e) {
                            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
                        }
                        if (!isset($subscription) || $subscription->status != 'active') {
                            $_subscription->deactivate();
                            $_subscription->save();
                        }
                    }
                }

            }
            log_message('CRON_SUCCESS', __FUNCTION__);
        }
    }

}