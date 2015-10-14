<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Omnipay\Common\Message\ResponseInterface as OmnipayResponseInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Payment
 */
class Payment extends Base_Controller
{

    /**
     * try to pay
     *
     * @param      $transactionId
     * @param null $planPeriodId
     */
    public function pay($transactionId, $planPeriodId = null)
    {
        $transaction = new Payment_transaction($transactionId);
        $this->throwModelNotFoundException($transaction);

        if (!$transaction->isPending()) {
            $this->throwAccessDeniedException('The transaction cannot be processed!');
        }

        $paymentGateway = Payment_gateways::findOneActiveBySlug($transaction->system);
        $this->throwModelNotFoundException($paymentGateway);

        $parameters = $this->generateParameters($transaction);

        if ($paymentGateway->isSlug('stripe')) {
            if ($this->isRequestMethod('post') &&
                ($stripeToken = $this->getRequest()->request->get('stripeToken', null))
            ) {
                $parameters['token'] = $stripeToken;
                \Stripe\Stripe::setApiKey($paymentGateway->getFieldValue('apiKey'));
                $user = $this->getUser();
                if (!$user->stripe_id) {

                    $customer = \Stripe\Customer::create(array(
                        'email' => $user->email
                    ));

                    $user->stripe_id = $customer->id;
                    $user->save();

                } else {
                    $customer = \Stripe\Customer::retrieve($user->stripe_id);
                }
                try {
                    $response = $customer->subscriptions->create(array(
                        'plan' => $planPeriodId,
                        'source' => $parameters['token']
                    ));
                } catch(Exception $e) {
                    $this->addFlash($e->getMessage(), 'error');
                    $this->template->set('publishableApiKey', $paymentGateway->getFieldValue('publishableApiKey'));
                    $this->template->set('transaction', $transaction);
                    $this->template->current_view = 'payment/gateway/stripePopup';
                    $this->template->render('default');
                    return;
                }

                $this->get('core.payment.transactions.manager')
                    ->completeStripe($transaction, $response);

                $redirectResponse = RedirectResponse::create(site_url('payment/success'));
                $this->sendResponse($transaction, $redirectResponse);

            } else {
                $this->template->set('publishableApiKey', $paymentGateway->getFieldValue('publishableApiKey'));
                $this->template->set('transaction', $transaction);
                $this->template->current_view = 'payment/gateway/stripePopup';
                $this->template->render('default');
                return;
            }
        }

        $paymentProvider = $this->get('core.payment.system.provider');
        $paymentProvider->setGateway($paymentGateway);
        $response = $paymentProvider->purchase($parameters);

        $this->processResponse($response, $transaction);
    }

    /**
     * Try ro complete transaction
     *
     * @param $transactionId
     */
    public function complete($transactionId)
    {
        $transaction = new Payment_transaction($transactionId);
        $this->throwModelNotFoundException($transaction);

        if (!$transaction->isPending()) {
            $this->throwAccessDeniedException('The transaction cannot be processed!');
        }

        $paymentGateway = Payment_gateways::findOneActiveBySlug($transaction->system);
        $this->throwModelNotFoundException($paymentGateway);

        $paymentProvider = $this->get('core.payment.system.provider');
        $paymentProvider->setGateway($paymentGateway);

        $parameters = $this->generateParameters($transaction);

        $response = $paymentProvider->completePurchase($parameters);

        $this->processResponse($response, $transaction);
    }

    /**
     * Set success flash message and redirect to forvard url
     */
    public function success()
    {
        $this->addFlash('Payment was successfully completed', 'success');
        $this->forwardUser();
    }

    /**
     * Process Omnipay Response
     *
     * @param OmnipayResponseInterface $response
     * @param Payment_transaction $transaction
     */
    protected function processResponse(OmnipayResponseInterface $response, Payment_transaction $transaction)
    {
        if ($response->isSuccessful()) {
            $this->get('core.payment.transactions.manager')
                ->complete($transaction, $response);

            $redirectResponse = RedirectResponse::create(site_url('payment/success'));
            $this->sendResponse($transaction, $redirectResponse);

        } elseif ($response->isRedirect()) {
            $response->redirect(); // this will automatically forward the customer
        } else {
            show_error($response->getMessage());
        }
    }

    /**
     * Get parameters for transaction
     *
     * @param Payment_transaction $transaction
     *
     * @return array
     */
    protected function generateParameters(Payment_transaction $transaction)
    {

        $parameters = array(
            'transactionId' => $transaction->getUniqId(),
            'amount' => $transaction->getAmount(),
            'description' => $transaction->description,
            'currency' => $transaction->currency,
            'returnUrl' => site_url('payment/complete/'.$transaction->id),
            'cancelUrl' => site_url(),
        );

        return $parameters;
    }

    /**
     * Send response
     *
     * @param Payment_transaction $transaction
     * @param RedirectResponse $response
     */
    protected function sendResponse(Payment_transaction $transaction, RedirectResponse $response)
    {
        switch ($transaction->system) {
            case 'authorize.net_sim':
                $response->sendContent();
                break;
            default:
                $response->send();
                break;
        }

        exit;
    }

}
