<?php

class Subscriptions_cron extends CLI_controller {

    /**
     * Check paypal data. If acccount expired - block it
     *
     * @access public
     * @return void
     */
    public function run() {
        try {
            $subscriptions = new Subscription();

            $allSubscriptions = $subscriptions->get();

            foreach($allSubscriptions as $_subscription) {

                if( $_subscription->end_date <= strtotime('now') ) {
                    $user = new User($_subscription->user_id);
                    if( $user->id != null ) {
                        $user->active = false;
                        $user->save();
                    }
                }

            }
            log_message('CRON_SUCCESS', __FUNCTION__);
        } catch(Exception $e) {
            log_message('CRON_ERROR', __FUNCTION__ . ' > ' . $e->getMessage());
        }
    }

}