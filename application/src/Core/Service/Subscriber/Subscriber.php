<?php
/**
 * Service for payment of subscriptions  
 * 
 * @author ajorjik
 */

namespace Core\Service\Subscriber;

use Subscription;
use Payment_transaction;
use User;
use DateTime;
use DateInterval;
use object;
use Plan;

/**
 * Class Subscriber
 */
class Subscriber 
{
    /**
     * @var object
     */
    private $user;
    
    /**
     * @var object
     */
    private $lastSubscription;
    
    /**
     * @var bool
     */
    private $hasActiveSubscription;
        
    /**
     * @var int
     */
    private $subscription;
    
    /**
     * @var string
     */
    private $dateFormat = 'Y-m-d';

    /**
     * Set current user
     *
     * @access public
     *
     * @param User $user object of model User
     *
     * @return object Subscriber
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->resetData();
        return $this;
    }
    
    /**
     * Check if user has an active payment at that moment
     *
     * @return bool  
     */
    public function hasActiveSubscription()
    {   
        if (!isset($this->hasActiveSubscription)) {
            $this->hasActiveSubscription = $this->user->hasActiveSubscription();
        }
        
        return $this->hasActivePayment;
    }
    
    /**
     * Get last user's payment
     *
     * @return object of model Payment | null 
     */
    public function getLastSubscription()
    {
        if (!isset($this->lastSubscription)) {
            $this->lastSubscription = $this->user->getLastSubscription();
        }
        
        return $this->lastSubscription;
    }
    
    /**
     * Get subscription by id of payment 
     *
     * @param $paymentId string id of payment
     * @return  Object Subscription | null       
     */
    public function getSubscriptionByPaymentId($paymentId)
    {
        $subscription = $this->user->subscription->getByPaymentId($paymentId);
        return $subscription;
    }

    /**
     * Add new payment
     *
     * @param Payment_transaction $transaction
     * @param Plan $plan
     * @param DateInterval|object $interval object DateInterval
     * @return Subscription
     */
     public function addNewSubscription (Payment_transaction $transaction, Plan $plan, DateInterval $interval )
     {
         $start = new DateTime();
         $lastSubscription = $this->getLastSubscription();
         if ($lastSubscription) {
             $lastEnd = DateTime::createFromFormat($this->dateFormat, $lastSubscription->end_date);
             if ($lastEnd > $start){
                 $start = $lastEnd->modify('+1 day');
             }
         }
         $end = clone $start;
         $end->add($interval);

         $subscription = new Subscription();
         $subscription->start_date = $start->format($this->dateFormat);
         $subscription->end_date = $end->format($this->dateFormat);
         $subscription->created = time();
         $subscription->save(array($transaction, $this->user, $plan));

         return $subscription;
     }

    /**
     * Add trial subscription
     *
     * @param Plan $plan
     * @param DateInterval|object $interval object DateInterval
     * @return Subscription
     */
    public function addTrialSubscription (Plan $plan, DateInterval $interval )
    {
        $start = new DateTime();
        $lastSubscription = $this->getLastSubscription();
        if ($lastSubscription) {
            $lastEnd = DateTime::createFromFormat($this->dateFormat, $lastSubscription->end_date);
            if ($lastEnd > $start){
                $start = $lastEnd->modify('+1 day');
            }
        }
        $end = clone $start;
        $end->add($interval);

        $subscription = new Subscription();
        $subscription->start_date = $start->format($this->dateFormat);
        $subscription->end_date = $end->format($this->dateFormat);
        $subscription->created = time();
        $subscription->status = $subscription::STATUS_ACTIVE;
        $subscription->is_stripe_active = false;
        $subscription->save(array($this->user, $plan));

        return $subscription;
    }
     
    /**
     * Reinitialize variables for Subscriber object
     *     
     */
    protected function resetData()
    {
        $this->lastPayment = null;
        $this->hasActivePayment = null;
    }

    

}  