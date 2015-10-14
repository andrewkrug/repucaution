<?php

class Subscription extends DataMapper {
    
    const STATUS_ACTIVE = 1;
    const STATUS_NOT_ACTIVE = 0;
    
    var $table = 'subscriptions';
   
    var $has_one = array('user', 'payment_transaction', 'plan');
    
    var $has_many = array();

    var $validation = array();


    /**
     * Get Subscription object by Payment_transaction object's id
     *
     * @param $paymentTransactionId
     * @return object Subscription | null
     * @internal param string $paymentId
     */
    public function getByPaymentTransactionId($paymentTransactionId){
       
        $subscription = $this->get_by_payment_transaction_id($paymentTransactionId);
        $result = ($subscription->exists()) ? $subscription : null;

        return $result;
    }

    /**
     * Get subscriptions of user by user id
     *
     * @param int  $userId
     * @param bool $exclude_active
     *
     * @return object Subscription | null
     */
    public function getUserSubscriptions($userId, $exclude_active = false) {

        $query = array(
           'user_id' => $userId
        );
        if($exclude_active) {
            $query['status !='] = Subscription::STATUS_ACTIVE;
        }
        $subscription = $this->include_related('payment_transaction')->where($query)->get();
        $result = ($subscription->exists()) ? $subscription : null;

        return $result;
    }

    /**
     * Check if status is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    /**
     * Set status to "active" if it does not
     */
    public function activate()
    {
        if ($this->isActive()) {
            return;
        }

        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }

    /**
     * Set status to "not active" if it does not
     */
    public function deactivate()
    {
        if (!$this->isActive()) {
            return;
        }

        $this->status = self::STATUS_NOT_ACTIVE;
        $this->save();
    }

}