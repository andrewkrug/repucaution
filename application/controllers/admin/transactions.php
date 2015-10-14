<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Transactions extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        CssJs::getInst()->add_js(array('libs/jquery.sortable.js',
                                        'controller/admin/transactions.js',
                                        'controller/admin/transactions_pagination.js'
        ));
        $this->load->config('transactions');
        $this->lang->load('transactions', $this->language);
        JsSettings::instance()->add([
            'i18n' => $this->lang->load('transactions', $this->language)
        ]);
    }

    public function index()
    {
        $request = $this->getRequest();
        $limit = $this->config->config['transactions_on_page'];
        $offset = ($page = $request->query->get('page', 1)) ? ($page-1)*$limit : '';
        $filter = $request->query->get('filter', null);
        JsSettings::instance()->add(array(
                                          'filter' => $filter,
                                          'limit' => $limit
        ));
        $transactions = Payment_transaction::getFiltered($limit, $offset, $filter);
        $this->template->set('transactions', $transactions);
        $this->template->set('page', $page);
        $this->template->set('filter', $filter);
        $this->template->render();

    }


}