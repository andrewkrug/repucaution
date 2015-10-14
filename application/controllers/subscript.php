<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Omnipay\Common\Message\ResponseInterface as OmnipayResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Payment
 */
class Subscript extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->config('manage_plans');
        if ($this->ion_auth->getActiveUser()) {
            $this->website_part = 'settings';
        }
        $this->template->set('showHeaderLinks', false);
        $this->template->layout = 'layouts/default';
    }

    /**
     * Show list of plans
     */
    public function plans()
    {
        if (!$this->ion_auth->logged_in()) {
            redirect('/auth');
        }

        $user = $this->getUser();

        $feature = new Feature();
        $plan = new Plan();
        $this->template->layout = 'layouts/customer_settings';
        $this->template->set('features', $feature->get());
        $this->template->set('plans', $plan->getActualPlansWithoutActive($user));
        $this->template->set('user', $user);
        $this->template->set('options', $this->config->config['period_qualifier']);
        $this->template->render();
    }

    public function special($planId, $inviteCode)
    {
        if (!$this->ion_auth->logged_in()) {
            $this->session->set_userdata('redirect_uri', 'subscript/special/'.$planId.'/'.$inviteCode);
            redirect('/auth');
        } else {
            if ($planId && $inviteCode) {
                $userId = $this->c_user->id;
                redirect('subscript/subscribe/'.$userId.'/'.$planId.'/'.$inviteCode);
            } else {
                show_404();
            }
        }

    }

    /**
     * Render View
     *
     * @param int  $userId
     * @param int  $planId
     * @param null|string $inviteCode
     */
    public function subscribe($userId, $planId, $inviteCode = null)
    {
        $user = new User($userId);
        $plan = new Plan($planId);

        if ($plan->special) {
            $specialInvite = new Special_invite();
            if (!$specialInvite->check($planId, $inviteCode)) {
                redirect('subscript/plans');
            }
        }
        //set any errors and display the form
        $message = strip_tags($this->template->message());
        if ($message) {
            $this->addFlash($message);
        }
        $periods = $plan->getPeriods();
        $systems = Payment_gateways::findAllActive()->all_to_single_array('name');
        $this->template->set('user', $user);
        $this->template->set('plan', $plan);
        $this->template->set('periods', $periods);
        $this->template->set('systems', $systems);
        $this->template->set('options', $this->config->config['period_qualifier']);
        $this->template->render();
    }

    public function transaction($userId, $planId)
    {
        $user = new User($userId);
        $plan = new Plan($planId);
        $request = $this->getRequest()->request;
        $planPeriodId = $request->get('plan_period');
        $planPeriod = new Plans_period($planPeriodId);
        $systemId = $request->get('system');
        $system = new Payment_gateways($systemId);
        $interval = new DateInterval('P'.$planPeriod->period.ucwords($planPeriod->qualifier));
        /* @var Core\Service\Subscriber\Subscriber $subscriber */
        $subscriber = $this->get('core.subscriber');
        $subscriber->setUser($user);
        if (!($planPeriod->price == 0 && $plan->isTrial())) {
            $transactionManager = $this->get('core.payment.transactions.manager');
            $transaction = $transactionManager->createForSubscription(array(
                'amount' => $planPeriod->price,
                'description' => 'subscribe on '.$plan->name,
            ), $user, $system);
        } else {
            $subscriber->addTrialSubscription($plan, $interval);
            if (!$this->ion_auth->logged_in()) {
                $this->ion_auth->loginForce($user->email, true);
            }
            redirect('dashboard');
        }
        $subscriber->addNewSubscription($transaction, $plan, $interval);
        if (!$this->ion_auth->logged_in()) {
            $this->ion_auth->loginForce($user->email, true);
        }

        redirect('payment/pay/'.$transaction->id.'/'.$planPeriodId);

    }

}
