<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriptions extends MY_Controller {

    protected $website_part = 'settings';

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('subscriptions_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('subscriptions_settings', $this->language)
        ]);
    }

    /**
     * Display subscriptions of user
     */
    public function index() 
    {               
        $subscription = new Subscription();
        $subscriptions = $subscription->getUserSubscriptions($this->c_user->id, true);
        CssJs::getInst()
            ->add_css('dev/admin.css')
            ->c_js('settings/subscriptions', 'index');;
        $this->template->set('subscriptions', $subscriptions);
        $this->template->set('active_subscription', $this->c_user->getLastSubscription());
        $this->template->render();
    }

    public function cancel_subscription() {
        $subscription = $this->c_user->getLastSubscription();
        if($this->c_user->stripe_id) {
            $paymentGateway = Payment_gateways::findOneActiveBySlug('stripe');
            if($paymentGateway->exists()) {
                \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
                $customer = \Stripe\Customer::retrieve($this->c_user->stripe_id);
                $paymentTransaction = $subscription->payment_transaction->get();
                if($paymentTransaction->exists()) {
                    $customer->subscriptions->retrieve($paymentTransaction->payment_id)->cancel();
                }
            }
        }
        $subscription->is_stripe_active = false;
        $subscription->save();
        redirect('settings/subscriptions');
    }
}