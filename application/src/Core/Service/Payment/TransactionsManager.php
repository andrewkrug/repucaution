<?php
/**
 * User: alkuk
 * Date: 03.06.14
 * Time: 16:09
 */

namespace Core\Service\Payment;

use Payment_transaction;
use User;
use Payment_gateways;
use RuntimeException;
use InvalidArgumentException;
use Omnipay\Common\Message\ResponseInterface;
use Core\Service\Payment\GatewayResponseInfoExtractorFactory;

class TransactionsManager
{
    const TARGET_SUBSCRIPTION = 'subscription';

    /**
     * @var string
     */
    protected $currency = 'usd';

    /**
     * @var \Core\Service\Payment\GatewayResponseInfoExtractorFactory
     */
    protected $infoExtractorFactory;

    /**
     * @var array
     */
    protected $options;

    public function __construct(GatewayResponseInfoExtractorFactory $infoExtractorFactory, array $options)
    {
        $this->infoExtractorFactory = $infoExtractorFactory;
        $this->options = $options;
    }

    /**
     * Create transaction for subscription
     *
     * @param array $data
     * @param User $user
     * @param Payment_gateways $gateway
     *
     * @return Payment_transaction
     */
    public function createForSubscription(array $data, User $user, Payment_gateways $gateway)
    {
        $data = array_merge($data, array(
            'target' => self::TARGET_SUBSCRIPTION,
        ));

        return $this->create($data, $user, $gateway);
    }

    /**
     * Create transaction
     *
     * @param array $data
     * @param User $user
     * @param Payment_gateways $gateway
     *
     * @return Payment_transaction
     */
    public function create(array $data, User $user, Payment_gateways $gateway)
    {
        if (!$gateway->exists() || empty($gateway->slug)) {
            throw new InvalidArgumentException('Invalid Payment gateway.');
        }

        if (!$user->exists()) {
            throw new InvalidArgumentException('Invalid User.');
        }

        $now = time();

        $data = array_merge($data, array(
            'currency' => $this->currency,
            'created' => $now,
            'updated' => $now,
            'status' => Payment_transaction::STATUS_PENDING,
            'user_id' => $user->id,
            'user_info' => '#'.$user->id.' '.$user->email.' '.$user->first_name.' '.$user->last_name,
            'system' => $gateway->slug,
        ));

        $this->validData($data);

        $paymentTransaction = new Payment_transaction();
        $paymentTransaction->from_array($data);
        if (!$paymentTransaction->save()) {
            throw new RuntimeException(
                sprintf('Cannot save transaction: %s.', $paymentTransaction->error->string)
            );
        }

        return $paymentTransaction;
    }

    /**
     * Complete payment
     *
     * @param Payment_transaction $transaction
     * @param ResponseInterface $response
     *
     * @throws \RuntimeException
     */
    public function complete(Payment_transaction $transaction, ResponseInterface $response)
    {
        if (!$transaction->isPending()) {
            throw new RuntimeException('Can complete only pending transaction.');
        }
        if (!$response->isSuccessful()) {
            throw new RuntimeException('Can process only success response.');
        }

        $infoExtractor = $this->infoExtractorFactory->create($response);

        $updateData = array(
            'updated' => time(),
            'status' => Payment_transaction::STATUS_COMPLETED,
            'test_mode' => $infoExtractor->isTestMode(),
            'payment_id' => $infoExtractor->getPaymentId(),
        );

        $transaction->from_array($updateData);
        $transaction->save();
        $this->processTarget($transaction);

    }

    /**
     * Complete payment
     *
     * @param Payment_transaction $transaction
     * @param ResponseInterface $response
     *
     * @throws \RuntimeException
     */
    public function completeStripe(Payment_transaction $transaction, \Stripe\Subscription $response)
    {
        if (!$transaction->isPending()) {
            throw new RuntimeException('Can complete only pending transaction.');
        }

        $updateData = array(
            'updated' => time(),
            'status' => Payment_transaction::STATUS_COMPLETED,
            'test_mode' => !$response->plan->livemode,
            'payment_id' => $response->id,
        );

        $transaction->from_array($updateData);
        $transaction->save();
        $this->processTarget($transaction);

    }

    /**
     * Throw an exception if data is invalid
     *
     * @param array $data
     *
     * @throws \RuntimeException
     */
    protected function validData(array $data)
    {
        $requiredKeys = array(
            'currency',
            'target',
            'amount',
            'description',
            'system',
            'user_id',
            'user_info',
        );

        foreach ($requiredKeys as $requiredKey) {
            if (empty($data[$requiredKey])) {
                throw new RuntimeException(
                    InvalidArgumentException('Provide %s parameter for transaction.', $requiredKey)
                );
            }
        }

        if (isset($this->options['minimal_amount'])) {
            $minAmount = $this->options['minimal_amount'];
            if ($data['amount'] < $minAmount) {
                $minAmountFormated = number_format(floatval($minAmount/100), 2);
                throw new InvalidArgumentException(
                    sprintf(
                        'Minimal transaction`s amount should not be less than %s. Please contact to Administrator.',
                        $minAmountFormated
                    )
                );
            }
        }

    }

    /**
     * Process transaction's target
     *
     * @param Payment_transaction $transaction
     */
    protected function processTarget(Payment_transaction $transaction)
    {
        switch ($transaction->target) {
            case self::TARGET_SUBSCRIPTION:
                $subscription = $transaction->subscription->get();
                if ($subscription->exists()) {
                    $subscription->activate();
                }
                break;
        }
    }
}
