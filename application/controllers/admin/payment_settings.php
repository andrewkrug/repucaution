<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_settings extends Admin_Controller
{

    public function __construct() {
        parent::__construct();
        $this->lang->load('payment_settings', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('payment_settings', $this->language)
        ]);
    }

    public function index()
    {

        $paymentData = $this->getPaymentSettings();

        if ($this->isRequestMethod('post') &&
            ($settings = $this->getRequest()->request->get('settings'))
        ) {
            $settings = Arr::remove($settings, array_keys($paymentData));
            $saved_settings = 0;
            $systemSettingsModel = $this->get('core.system.settings.model');
            foreach ($settings as $setting=>$value) {
                $systemSettingsModel->setData($setting, $value);
                $saved_settings++;
            }

            if ($saved_settings) {
                $this->addFlash(lang('payment_update_success'), 'success');
                redirect(current_url());
            }

        }

        $geteways = Payment_gateways::findAll();

        $this->template->set('geteways', $geteways);
        $this->template->set('paymentData', $paymentData);
        $this->template->render();
    }

    public function gateways($slug)
    {
        $gateway = Payment_gateways::findOneBySlug($slug);
        $this->throwModelNotFoundException($gateway);

        if ($this->isRequestMethod('post')) {
            $data = $this->getRequest()->request->get('gateway');
            if (empty($data['enable'])) {
                $data['enable'] = 0;
            }

            $gateway->handleData($data);
            if ($gateway->save()) {
                $this->addFlash(lang('gateway_update_success'), 'success');
                redirect(current_url());
            }

            $this->addFlash($gateway->errors->string);
        }

        $this->template->set('gateway', $gateway);
        $this->template->render();
    }

    /**
     * Generate available payment options
     *
     * @return array
     */
    protected function getPaymentSettings()
    {
        $paymentData = array(
            'payment_enable' => array(
                'label' => lang('payment_gateway'),
                'status' => $this->get('core.status.system')->isPaymentEnabled(),
            ),
            'payment_sandbox_mode' => array(
                'label' => lang('payment_sandbox_mode'),
                'status' => $this->get('core.status.system')->isSandboxPayment(),
            ),
        );

        foreach ($paymentData as &$data) {
            $data['text_status'] = $this->getStatusButtonName($data['status']);
            $data['value'] = (int)!$data['status'];
        }

        return $paymentData;
    }

    /**
     * Generate button name
     *
     * @param bool $status
     *
     * @return string
     */
    protected function getStatusButtonName($status)
    {
        return $status ? lang('disable') : lang('enable');
    }

}