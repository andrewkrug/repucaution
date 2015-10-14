<?php
/**
 * User: alkuk
 * Date: 28.05.14
 * Time: 0:17
 */

namespace Core\Service\Status;

use System_setting;

class SystemStatus
{
    /**
     * @var array
     */
    protected $properties = array();

    /**
     * @var \System_setting
     */
    protected $systemSettingModel;

    public function __construct(System_setting $systemSettingModel)
    {
        $this->systemSettingModel = $systemSettingModel;
    }

    /**
     * Check is payment enabled
     *
     * @return bool
     */
    public function isPaymentEnabled()
    {
        $key = 'payment_enable';
        $transformer = $this->getTransformer($key);

        return $this->getFromSystemSettingModel($key, $transformer);
    }

    /**
     * Check is trial enabled
     *
     * @return bool
     */
    public function isTrialEnabled()
    {
        $key = 'trial_enabled';
        $transformer = $this->getTransformer($key);

        return $this->getFromSystemSettingModel($key, $transformer);
    }

    /**
     * Check is payment in sandbox mode
     *
     * @return mixed
     */
    public function isSandboxPayment()
    {
        $key = 'payment_sandbox_mode';
        $transformer = $this->getTransformer($key);

        return $this->getFromSystemSettingModel($key, $transformer);
    }

    protected function getTransformer($key)
    {
        switch ($key) {
            case 'payment_enable':
            case 'payment_sandbox_mode':
            case 'trial_enabled':
                return function ($data) {
                    return (bool)$data;
                };
            default:
                return null;
        }
    }

    /**
     * Get value from System_status model
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getFromSystemSettingModel($key, \Closure $transformer = null)
    {
        if (!isset($this->properties[$key])) {
            $value = $this->systemSettingModel->getData($key);
            if ($transformer) {
                $value = $transformer($value);
            }

            $this->properties[$key] = $value;
        }

        return $this->properties[$key];
    }
}
