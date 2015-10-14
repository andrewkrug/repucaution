<?php

class Stripe_subscriptions_cron extends CLI_controller {

    /**
     * Check stripe data.
     *
     * @access public
     * @return void
     */
    public function run() {
        try {
            $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
            if($paymentGateway->exists()) {
                \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
                $subscriptions = new Subscription();

                $allSubscriptions = $subscriptions->get();

                /* @var Subscription $_subscription */
                foreach($allSubscriptions as $_subscription) {

                    if( $_subscription->end_date <= strtotime('now') ) {
                        $paymentTransaction = $_subscription->payment_transaction->get();
                        if ($paymentTransaction->system == 'stripe') {
                            $user = new User($_subscription->user_id);
                            $customer = \Stripe\Customer::retrieve($user->stripe_id);
                            $subscription = $customer->subscriptions->retrieve($paymentTransaction->payment_id);
                            if ($subscription->status == 'active') {
                                $date = new DateTime();
                                $date->setTimestamp($subscription->current_period_end);
                                $_subscription->end_date = $date->format('Y-m-d');
                                $_subscription->activate();
                                $_subscription->save();
                            }
                        }
                    }

                }
                log_message('CRON_SUCCESS', __FUNCTION__);
            } else {
                log_message('CRON_ERROR', __FUNCTION__ . ' > ' . 'No Stripe Api key.');
            }
        } catch(Exception $e) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }

}