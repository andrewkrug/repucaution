<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Manage_plans extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        CssJs::getInst()->add_js(array('libs/jquery.sortable.js',
                                        'controller/admin/manage_plans.js'
        ));
        $this->lang->load('manage_plans', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('manage_plans', $this->language)
        ]);
        $this->load->config('manage_plans');
    }

    public function index()
    {
        CssJs::getInst()->add_js(array('libs/jquery.tablednd.js'));
        $options = $this->config->config['period_qualifier'];
        $plans = new Plan();
        $plans->getActualPlans(true, true);
        $this->template->set('plans', $plans);
        $this->template->set('options', $options);
        $this->template->render();

    }

    public function add()
    {
        redirect('/admin/manage_plans/edit');
    }

    public function edit()
    {
        $options = $this->config->config['period_qualifier'];
        $error = array();
        $plan = new Plan();
        $features = new Feature();
        $request = $this->getRequest();

        //check if data was sent
        if ($this->isRequestMethod('POST')) {
            $weight = ($request->request->get('plan_weight', '')) ? : (int)$plan->getActualPlans()->weight+1;
            $plan->clear();
            $plan->get_by_id($request->request->get('id'));
            $plan->weight = $weight;
            $plan->name = $request->request->get('name');
            $trial = $request->request->get('trial');
            $plan->trial = !empty($trial);
            $special = $request->request->get('special');
            $plan->special = !empty($special);
            $featuresIds = $request->request->get('feature');
            $saveFeatures = array();
            $saveFeaturesIds = array();
            //check if features was selected
            if (implode('', $featuresIds) == '') {
                $error[] = lang('attached_features_count_error');
            } else {
                $weight = 1;
                //save features of plan
                foreach ($featuresIds as $id) {
                    if ($id) {
                        $plansFeature = new Plans_feature($request->request->get('feature_'.$id.'_plansfeatureid', ''));
                        $plansFeature->feature_id = $id;
                        if ($request->request->has('feature_'.$id.'_value')) {
                            $value = $request->request->get('feature_'.$id.'_value');
                            $featureValidator = new Feature($id);
                            if ($featureValidator->validValue($value)) {
                                $plansFeature->value = $value;
                            } else {
                                $error[] = lang('attached_features_type_error', [$featureValidator->name, $featureValidator->type]);
                            }

                        }
                        $plansFeature->weight = $weight;
                        if ($plansFeature->save()) {
                            $saveFeatures[] = $plansFeature;
                            $saveFeaturesIds[] = $plansFeature->id;
                            $weight++;
                        }

                    }
                }
            }
            //save periods of plan
            $savePeriods = array();
            $savePeriodsIds = array();
            if ($count = count($request->request->get('period_id'))) {
                $periodId = $request->request->get('period_id');
                $period = $request->request->get('period');
                $qualifier = $request->request->get('qualifier');
                $price = $request->request->get('price');
                for ($i = 0; $i < $count; $i++) {
                    $planPeriod = new Plans_period($periodId[$i]);
                    $planPeriod->period = $period[$i];
                    $planPeriod->qualifier = $qualifier[$i];
                    $planPeriod->price = $price[$i];
                    if ($planPeriod->save()) {
                        $savePeriods[] = $planPeriod;
                        $savePeriodsIds[] = $planPeriod->id;
                    }

                    if ($planPeriod->error->string) {
                        $error[] = $planPeriod->error->string;
                    }
                }
            }
            //save plan with related features and period
            if (empty($error)){
                $plan->save(array($saveFeatures, $savePeriods));

                $plan->createStripePlans();
            }
            if ($plan->errors->string) {
                $error[] = $plan->errors->string;
            }
            if (!empty($error)) {
                $this->addFlash(implode('', $error));
                $this->session->set_flashdata('recent', $request->request->all());
                $planParam = ($plan->id) ? '?plan='.$plan->id : '';
                redirect('/admin/manage_plans/edit'.$planParam);
            } else {
                $plan->deleteOldPlanPeriods($savePeriodsIds);
                $plan->deleteOldPlanfeatures($saveFeaturesIds);
            }

            redirect('/admin/manage_plans/');
        }
        $planId = $request->query->get('plan', '');
        $plan->get_by_id($planId);
        $plansFeatures = $plan->getAttachedFeatures();
        $plansPeriod = $plan->plans_period->get();

        //fill data after bad validation
        if ($recent = $this->session->flashdata('recent')) {

            //recent selected features
            foreach ($plansFeatures as $planFeature) {
                $featureId = $planFeature->feature_id;
                if (in_array($featureId, $recent['feature'])) {
                    $recent['feature'] = array_diff($recent['feature'], array($featureId));
                }
            }

            //recent selected periods
            $coup = count($recent['period']);
            foreach ($plansPeriod as $planPeriod) {
                for ($i=0; $i<$coup; $i++) {
                    if ($planPeriod->period == $recent['period'][$i] &&
                        $planPeriod->qualifier == $recent['qualifier'][$i] &&
                        $plansPeriod->price == $recent['price'][$i]) {
                        $recent['period_id'][$i] = $planPeriod->id;
                    } else {
                        $recent['period_id'][$i] = '';
                    }
                }
            }

            $this->template->set('recent', $recent);
        }

        $features->getFreeFeatures($plansFeatures);
        CssJs::getInst()->add_js('libs/test.js');
        //setting variables of template
        $this->template->set('options', $options);
        $this->template->set('features', $features);
        $this->template->set('plansFeatures', $plansFeatures);
        $this->template->set('plansPeriod', $plansPeriod);
        $this->template->set('plan', $plan);
        $this->template->render();
    }

    public function delete()
    {
        $request = $this->getRequest();
        if ($planId = $request->query->get('plan', '')) {
            $plan = new Plan($planId);
            if ($plan->exists() && !$plan->deleted) {
                $plan->deleted = 1;
            }
            if ($plan->save()) {
                $this->addFlash(lang('plan_delete_success'), 'success');
            } else {
                $this->addFlash(lang('plan_delete_error'));
            }
        }

        redirect('admin/manage_plans');
    }

    public function resort()
    {
        $request = $this->getRequest();
        if ($request->request) {
            $post = $request->request->all();
            foreach ($post as $id=>$weight) {
                $plan = new Plan($id);
                $plan->weight = $weight;
                $plan->save();
            }

            echo lang('plan_order_change_success');
        }
    }

    public function specialInvite()
    {
        $request = $this->getRequest()->request;
        $inviteCode = $this->ion_auth->createInviteCode();
        $email = $request->get('email','');
        $planId = $request->get('plan_id','');
        $plan = new Plan($planId);
        $specialInvite = new Special_invite();
        $specialInvite->plan_id = $planId;
        $specialInvite->invite_code = md5($inviteCode);
        $specialInvite->end_date = time()+$this->config->item('invite_timelimit');
        $specialInvite->save();
        if ($email && $plan->id && $specialInvite->id) {
            $sender = $this->get('core.mail.sender');
            $params['to'] = $email;
            $params['data'] = array(
                'auth_link' => site_url('subscript/special/'.$planId.'/'.$inviteCode),
                'register_link' => site_url('auth/register/'.$planId.'/'.$inviteCode),
                'sitename' => $this->config->config['OCU_site_name'],
                'plan' => $plan
            );

            if ($success = ($sender->sendSpecialInviteMail($params))) {
                $this->addFlash(lang('send_invite_success'), 'success');
            } else {
                $this->addFlash(lang('invite_error'));
            }

            echo json_encode(array('success' => $success));
        }
    }
}