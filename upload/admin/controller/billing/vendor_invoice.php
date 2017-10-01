<?php
defined('_PATH') or die('Restricted!');

class ControllerBillingVendorInvoice extends Controller {
    private $error = array();

    public function index() {
        $this->data = $this->load->language('billing/vendor_invoice');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/datetimepicker/moment.js');
        $this->document->addScript('view/javascript/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/javascript/datetimepicker/bootstrap-datetimepicker.min.css');

        $url = $this->build->url(array(
            'filter_invoice_id',
            
            'filter_name',
            'filter_total',
           
            'filter_transaction',
            'filter_date_due',
            'filter_date_issued',
            'filter_date_modified',
            'sort',
            'order',
            'page'
        ));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true)
        );

        if (isset($this->request->get['filter_invoice_id'])) {
            $filter_invoice_id = $this->request->get['filter_invoice_id'];
        } else {
            $filter_invoice_id = '';
        }


        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = null;
        }

      

        if (isset($this->request->get['filter_transaction'])) {
            $filter_transaction = $this->request->get['filter_transaction'];
        } else {
            $filter_transaction = null;
        }

        if (isset($this->request->get['filter_date_due'])) {
            $filter_date_due = $this->request->get['filter_date_due'];
        } else {
            $filter_date_due = '';
        }

        if (isset($this->request->get['filter_date_issued'])) {
            $filter_date_issued = $this->request->get['filter_date_issued'];
        } else {
            $filter_date_issued = '';
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = '';
        }

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'invoice_id';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        $filter_data = array(
            'filter_invoice_id'    => $filter_invoice_id,
            
            'filter_name'          => $filter_name,
            'filter_total'         => $filter_total,
            
            'filter_transaction'   => $filter_transaction,
            'filter_date_due'      => $filter_date_due,
            'filter_date_issued'   => $filter_date_issued,
            'filter_date_modified' => $filter_date_modified,
            'start'                => $this->config->get('config_limit_admin') * ($page - 1),
            'limit'                => $this->config->get('config_limit_admin'),
            'sort'                 => $sort,
            'order'                => $order
        );

        $this->load->model('billing/vendor_invoice');

        $this->data['invoices'] = array();

        $invoices = $this->model_billing_vendor_invoice->getInvoices($filter_data);

        foreach ($invoices as $invoice) {
            $this->data['invoices'][] = array(
                'invoice_id'       => $invoice['invoice_id'],
                'vendor'         => $this->url->link('billing/vendor/form', 'token=' . $this->session->data['token'] . '&vendor_id=' . $invoice['vendor_id'], true),
                'name'             => $invoice['vendor_name'],
                'total'            => $this->currency->format($invoice['total'], $invoice['currency_code'], $invoice['currency_value']),
               
                'transaction'      => $invoice['transaction'],
                'transaction_href' => $this->url->link('accounting/journal', 'token=' . $this->session->data['token'] . '&filter_invoice_id=' . $invoice['invoice_id'], true),
                'date_due'         => date($this->language->get('date_format_short'), strtotime($invoice['date_due'])),
                'date_issued'      => date($this->language->get('datetime_format_short'), strtotime($invoice['date_issued'])),
                'date_modified'    => date($this->language->get('datetime_format_short'), strtotime($invoice['date_modified'])),
                'edit'             => $this->url->link('billing/vendor_invoice/form', 'token=' . $this->session->data['token'] . $url . '&invoice_id=' . $invoice['invoice_id'], true),
                
                'view'             => $this->url->link('billing/vendor_invoice/view', 'token=' . $this->session->data['token'] . $url . '&invoice_id=' . $invoice['invoice_id'], true)
            );
        }

        $url = $this->build->url(array(
            'filter_invoice_id',
           
            'filter_name',
            'filter_total',
          
            'filter_transaction',
            'filter_date_due',
            'filter_date_issued',
            'filter_date_modified',
            'sort',
            'order'
        ));

        $pagination = new Pagination();
        $pagination->total = $this->model_billing_vendor_invoice->getTotalInvoices($filter_data);
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . '&page={page}' . $url, true);

        $this->data['pagination'] = $pagination->render();

        $this->data['transaction'] = $this->url->link('billing/vendor_invoice/transaction', 'token=' . $this->session->data['token'], true);
        $this->data['delete'] = $this->url->link('billing/vendor_invoice/delete', 'token=' . $this->session->data['token'], true);
        $this->data['insert'] = $this->url->link('billing/vendor_invoice/form', 'token=' . $this->session->data['token'], true);

        $this->data['token'] = $this->session->data['token'];

        $this->data['success'] = $this->build->data('success', $this->session->data);
        $this->data['error_warning'] = $this->build->data('warning', $this->error);
        $this->data['selected'] = $this->build->data('selected', $this->request->post, array(), array());

        $url = $this->build->url(array(
            'filter_invoice_id',
           
            'filter_name',
            'filter_total',
           
            'filter_transaction',
            'filter_date_due',
            'filter_date_issued',
            'filter_date_modified'
        ));

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        if ($order == 'ASC') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $this->data['sort_invoice_id'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=invoice_id&order=' . $order, true);
        $this->data['sort_name'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=vendor_name&order=' . $order, true);
        $this->data['sort_total'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=total&order=' . $order, true);
       
        $this->data['sort_date_issued'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=date_issued&order=' . $order, true);
        $this->data['sort_date_due'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=date_due&order=' . $order, true);
        $this->data['sort_date_modified'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=date_modified&order=' . $order, true);
        $this->data['sort_transaction'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url . '&sort=transaction&order=' . $order, true);

        $this->data['filter_invoice_id'] = $filter_invoice_id;
        $this->data['filter_name'] = $filter_name;
        $this->data['filter_total'] = $filter_total;
       
        $this->data['filter_transaction'] = $filter_transaction;
        $this->data['filter_date_due'] = $filter_date_due;
        $this->data['filter_date_issued'] = $filter_date_issued;
        $this->data['filter_date_modified'] = $filter_date_modified;

      //  $this->load->model('system/status');

      //  $this->data['statuses'] = $this->model_system_status->getStatuses();

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->render('billing/vendor_invoice_list'));
    }

    public function delete() {
        $this->load->language('billing/vendor_invoice');

        $this->load->model('billing/vendor_invoice');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $invoice_id) {
                $this->model_billing_vendor_invoice->deleteInvoice($invoice_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'], true));
        }

        $this->index();
    }

    public function form() {
        $this->data = $this->load->language('billing/vendor_invoice');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/datetimepicker/moment.js');
        $this->document->addScript('view/javascript/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/javascript/datetimepicker/bootstrap-datetimepicker.min.css');

        $url = $this->build->url(array(
            'filter_invoice_id',
            'filter_recurring_id',
            'filter_name',
            'filter_total',
            'filter_status_id',
            'filter_transaction',
            'filter_date_due',
            'filter_date_issued',
            'filter_date_modified',
            'sort',
            'order',
            'page',
            'invoice_id'
        ));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true)
        );

        $this->load->model('billing/vendor_invoice');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if (isset($this->request->get['invoice_id'])) {
                $this->model_billing_vendor_invoice->editInvoice((int)$this->request->get['invoice_id'], $this->request->post);
            } else {
                $this->model_billing_vendor_invoice->addInvoice($this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true));
        }

        if (isset($this->request->get['invoice_id'])) {
            $invoice_info = $this->model_billing_vendor_invoice->getInvoice((int)$this->request->get['invoice_id']);
        } else {
            $invoice_info = array();
        }

        $this->data['action'] = $this->url->link('billing/vendor_invoice/form', 'token=' . $this->session->data['token'] . $url, true);

        $this->data['cancel'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true);

        $this->data['token'] = $this->session->data['token'];

        $this->data['error_warning'] = $this->build->data('warning', $this->error);

        $this->data['invoice_id'] = $this->build->data('invoice_id', $this->request->post, $invoice_info);
        $this->data['vendor_id'] = $this->build->data('vendor_id', $this->request->post, $invoice_info);
       
        $this->data['phone'] = $this->build->data('phone', $this->request->post, $invoice_info);
        $this->data['address'] = $this->build->data('address', $this->request->post, $invoice_info);
        $this->data['email'] = $this->build->data('email', $this->request->post, $invoice_info);

        $this->data['name'] = $this->build->data('name', $this->request->post, $invoice_info);
        $this->data['payment_address'] = $this->build->data('payment_address', $this->request->post, $invoice_info);
        $this->data['payment_email'] = $this->build->data('payment_email', $this->request->post, $invoice_info);
        $this->data['payment_phone'] = $this->build->data('payment_phone', $this->request->post, $invoice_info);
        $this->data['total'] = $this->build->data('total', $this->request->post, $invoice_info);
        $this->data['date_due'] = $this->build->data('date_due', $this->request->post, $invoice_info);
        $this->data['payment_code'] = $this->build->data('payment_code', $this->request->post, $invoice_info);
        $this->data['payment_description'] = $this->build->data('payment_description', $this->request->post, $invoice_info);
        $this->data['currency_code'] = $this->build->data('currency_code', $this->request->post, $invoice_info);
        $this->data['currency_value'] = $this->build->data('currency_value', $this->request->post, $invoice_info);
        $this->data['date_modified'] = $this->build->data('date_modified', $this->request->post, $invoice_info);
        $this->data['invoiceno'] = $this->build->data('invoiceno', $this->request->post, $invoice_info);
        $this->data['date_issued'] = $this->build->data('date_issued', $this->request->post, $invoice_info, $this->config->get('config_currency'));
       
        $this->data['items'] = $this->build->data('items', $this->request->post, $invoice_info, array());
        $this->data['totals'] = $this->build->data('totals', $this->request->post, $invoice_info, array());

       

     

        $this->load->model('accounting/currency');

        $this->data['currencies'] = $this->model_accounting_currency->getCurrencies();

        $this->data['default_currency_code'] = $this->config->get('config_currency');

      

        $this->load->model('accounting/tax_class');

        $this->data['tax_classes'] = $this->model_accounting_tax_class->getTaxClasses();

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->render('billing/vendor_invoice_form'));
    }

    ////

    

    public function view() {
        $this->data = $this->load->language('billing/vendor_invoice');

        $this->document->setTitle($this->language->get('heading_title'));

        $url = $this->build->url(array(
            'filter_invoice_id',
            'filter_recurring_id',
            'filter_name',
            'filter_total',
            'filter_status_id',
            'filter_transaction',
            'filter_date_due',
            'filter_date_issued',
            'filter_date_modified',
            'sort',
            'order',
            'page',
            'invoice_id'
        ));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true)
        );

        $this->load->model('billing/vendor_invoice');

      if (isset($this->request->get['invoice_id'])) {
            $invoice_info = $this->model_billing_vendor_invoice->getInvoice((int)$this->request->get['invoice_id']);
        } else {
            $invoice_info = array();
        }

        if ($invoice_info) {
            $this->data['cancel'] = $this->url->link('billing/vendor_invoice', 'token=' . $this->session->data['token'] . $url, true);

            $this->data['invoice'] = $this->url->link('billing/vendor_invoice/invoice', 'token=' . $this->session->data['token'] . '&invoice_id=' . $invoice_info['invoice_id'], true);

            $this->data['token'] = $this->session->data['token'];

            /////////////

          $this->data['invoice_id'] = $this->build->data('invoice_id', $this->request->post, $invoice_info);
        $this->data['vendor_id'] = $this->build->data('vendor_id', $this->request->post, $invoice_info);
       
        $this->data['phone'] = $this->build->data('phone', $this->request->post, $invoice_info);
        $this->data['address'] = $this->build->data('address', $this->request->post, $invoice_info);
        $this->data['email'] = $this->build->data('email', $this->request->post, $invoice_info);

        $this->data['name'] = $this->build->data('name', $this->request->post, $invoice_info);
        $this->data['payment_address'] = $this->build->data('payment_address', $this->request->post, $invoice_info);
        $this->data['payment_email'] = $this->build->data('payment_email', $this->request->post, $invoice_info);
        $this->data['payment_phone'] = $this->build->data('payment_phone', $this->request->post, $invoice_info);
        $this->data['total'] = $this->build->data('total', $this->request->post, $invoice_info);
        $this->data['date_due'] = $this->build->data('date_due', $this->request->post, $invoice_info);
        $this->data['payment_code'] = $this->build->data('payment_code', $this->request->post, $invoice_info);
        $this->data['payment_description'] = $this->build->data('payment_description', $this->request->post, $invoice_info);
        $this->data['currency_code'] = $this->build->data('currency_code', $this->request->post, $invoice_info);
        $this->data['currency_value'] = $this->build->data('currency_value', $this->request->post, $invoice_info);
        $this->data['date_modified'] = $this->build->data('date_modified', $this->request->post, $invoice_info);
        $this->data['invoiceno'] = $this->build->data('invoiceno', $this->request->post, $invoice_info);
        $this->data['date_issued'] = $this->build->data('date_issued', $this->request->post, $invoice_info, $this->config->get('config_currency'));
       
        $this->data['items'] = $this->build->data('items', $this->request->post, $invoice_info, array());
        $this->data['totals'] = $this->build->data('totals', $this->request->post, $invoice_info, array());

            

            $this->load->model('accounting/currency');

            $currencies = $this->model_accounting_currency->getCurrencies();

            $this->data['currency_code'] = $invoice_info['currency_code'];

            foreach ($currencies as $currency) {
                if ($currency['code'] == $invoice_info['currency_code']) {
                    $this->data['currency_code'] = $currency['title'];
                }
            }

            $items = $invoice_info['items'];

            $this->data['items'] = array();

            $number = 1;

            foreach ($items as $item) {
                $this->data['items'][] = array(
                    'number'      => $number,
                    'title'       => html_entity_decode($item['title'], ENT_QUOTES),
                    'description' => html_entity_decode(nl2br($item['description']), ENT_QUOTES),
                    'quantity'    => $item['quantity'],
                    'price'       => $this->currency->format($item['price'], $invoice_info['currency_code'], $invoice_info['currency_value']),
                    'discount'    => (float)$item['discount'] ? $this->currency->format($item['discount'], $invoice_info['currency_code'], $invoice_info['currency_value']) : '-',
                    'total'       => $this->currency->format(($item['price'] - $item['discount']) * $item['quantity'], $invoice_info['currency_code'], $invoice_info['currency_value'])
                );

                $number++;
            }

            $totals = $invoice_info['totals'];

            $this->data['totals'] = array();

            foreach ($totals as $total) {
                $this->data['totals'][] = array(
                    'title' => html_entity_decode($total['title'], ENT_QUOTES),
                    'text'  => $this->currency->format($total['value'], $invoice_info['currency_code'], $invoice_info['currency_value'])
                );
            }

            $this->data['header'] = $this->load->controller('common/header');
            $this->data['footer'] = $this->load->controller('common/footer');

            $this->response->setOutput($this->render('billing/vendor_invoice_view'));
        } else {
            return new Action('error/not_found');
        }
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'billing/vendor_invoice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'billing/vendor_invoice')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }


 
    public function validate_step_1() {
        $this->load->language('billing/vendor_invoice');

        $json = array();

        if (!$this->user->hasPermission('modify', 'billing/vendor_invoice')) {
            $json['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 30)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ((utf8_strlen($this->request->post['address']) < 1) || (utf8_strlen($this->request->post['address']) > 100)) {
            $this->error['address'] = $this->language->get('error_address');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $json['email'] = $this->language->get('error_email');
        }

        if (!$this->request->post['date_due']) {
            $json['date_due'] = $this->language->get('error_date_due');
        }

        if (!$json) {
            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validate_step_3() {
        $this->load->language('billing/vendor_invoice');

        $json = array();

        if (!$this->user->hasPermission('modify', 'billing/vendor_invoice')) {
            $json['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['currency_code']) {
            $json['currency_code'] = $this->language->get('error_currency_code');
        }

        if (!(float)$this->request->post['currency_value']) {
            $json['currency_value'] = $this->language->get('error_currency_value');
        }

        if (!$json) {
            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function validate_step_4() {
        $json = array();

        $this->load->model('accounting/tax_class');

        if (isset($this->request->post['items'])) {
            $taxes = array();
            $total = 0;

            $json['items'] = array();

            $number = 1;

            foreach ($this->request->post['items'] as $key => $item) {
                $tax = 0;

                $tax_class_info = $this->model_accounting_tax_class->getTaxClass($item['tax_class_id']);

                if ($tax_class_info) {
                    foreach ($tax_class_info['tax_rates'] as $tax_rate) {
                        if ($tax_rate['type'] == 'P') {
                            $tax = ((float)$item['price'] - (float)$item['discount']) / 100 * $tax_rate['rate'];
                        } else {
                            $tax = $tax_rate['rate'];
                        }

                        if (isset($taxes[$tax_rate['tax_rate_id']])) {
                            $value = $tax * (int)$item['quantity'] + $taxes[$tax_rate['tax_rate_id']]['value'];
                        } else {
                            $value = $tax * (int)$item['quantity'];
                        }

                        $taxes[$tax_rate['tax_rate_id']] = array(
                            'name'  => $tax_rate['name'],
                            'value' => $value
                        );
                    }
                }

                $total += ((float)$item['price'] - (float)$item['discount']) * (int)$item['quantity'];

                $json['items'][] = array(
                    'key'         => $key,
                    'number'      => $number,
                    'title'       => $item['title'],
                    'description' => nl2br($item['description']),
                    'quantity'    => $item['quantity'],
                    'tax'         => $tax,
                    'price'       => $this->currency->format((float)$item['price'], $this->request->post['currency_code'], (float)$this->request->post['currency_value']),
                    'discount'    => (float)$item['discount'] ? $this->currency->format((float)$item['discount'], $this->request->post['currency_code'], (float)$this->request->post['currency_value']) : '-',
                    'total'       => $this->currency->format((((float)$item['price'] - (float)$item['discount']) * (int)$item['quantity']), $this->request->post['currency_code'], (float)$this->request->post['currency_value'])
                );

                $number++;
            }

            $this->load->model('extension/extension');

            $sort_order = array();

            $results = $this->model_extension_extension->getInstalled('total');

            foreach ($results as $key => $value) {
                $sort_order[$key] = $this->config->get($value . '_sort_order');
            }

            array_multisort($sort_order, SORT_ASC, $results);

            $total_data = array();

            foreach ($results as $result) {
                if ($this->config->get($result . '_status')) {
                    $this->load->model('total/' . $result . '/' . $result);

                    $this->{'model_total_' . $result . '_' . $result}->getTotal($total_data, $total, $taxes);
                }
            }

            $json['totals'] = array();

            foreach ($total_data as $total) {
                $json['totals'][] = array(
                    'code'       => $total['code'],
                    'title'      => $total['title'],
                    'text'       => $this->currency->format($total['value'], $this->request->post['currency_code'], $this->request->post['currency_value']),
                    'value'      => $total['value'],
                    'sort_order' => $total['sort_order']
                );
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}