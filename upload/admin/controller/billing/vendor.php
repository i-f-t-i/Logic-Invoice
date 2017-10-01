<?php
defined('_PATH') or die('Restricted!');

class ControllerBillingVendor extends Controller {
    private $error = array();

    public function index() {
        $this->data = $this->load->language('billing/vendor');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->document->addScript('view/javascript/datetimepicker/moment.js');
        $this->document->addScript('view/javascript/datetimepicker/bootstrap-datetimepicker.min.js');
        $this->document->addStyle('view/javascript/datetimepicker/bootstrap-datetimepicker.min.css');

        $url = $this->build->url(array(
            'filter_name',
            'filter_address',
            'filter_email',
            'filter_phone',
            'filter_date_added',
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
            'href' => $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url, true)
        );

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_address'])) {
            $filter_address = $this->request->get['filter_address'];
        } else {
            $filter_address = null;
        }

         if (isset($this->request->get['filter_email'])) {
            $filter_email = $this->request->get['filter_email'];
        } else {
            $filter_email = null;
        }

          if (isset($this->request->get['filter_phone'])) {
            $filter_phone = $this->request->get['filter_phone'];
        } else {
            $filter_phone = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = '';
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
            $sort = 'name';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        $filter_data = array(
            'filter_name'          => $filter_name,
            'filter_address'       => $filter_address,
            'filter_email'         => $filter_email,
            'filter_phone'         => $filter_phone,
            'filter_date_added'    => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'start'                => $this->config->get('config_limit_admin') * ($page - 1),
            'limit'                => $this->config->get('config_limit_admin'),
            'sort'                 => $sort,
            'order'                => $order
        );

        $this->load->model('billing/vendor');

        $this->data['vendors'] = array();

        $vendors = $this->model_billing_vendor->getVendors($filter_data);

        foreach ($vendors as $vendor) {
            $this->data['vendors'][] = array(
                'vendor_id'   => $vendor['vendor_id'],
                'name'      => $vendor['name'],
                'address'         => $vendor['address'],
                'email'  => $vendor['email'],
                'phone'  => $vendor['phone'],
                'date_added'    => date($this->language->get('datetime_format_short'), strtotime($vendor['date_added'])),
                'date_modified' => date($this->language->get('datetime_format_short'), strtotime($vendor['date_modified'])),
                'edit'          => $this->url->link('billing/vendor/form', 'token=' . $this->session->data['token'] . $url . '&vendor_id=' . $vendor['vendor_id'], true)
                
            );
        }

        $url = $this->build->url(array(
           'filter_name',
            'filter_vendor_id',
            'filter_email',
            'filter_phone',
            'filter_date_added',
            'filter_date_modified',
            'sort',
            'order'
        ));

        $pagination = new Pagination();
        $pagination->total = $this->model_billing_vendor->getTotalvendors($filter_data);
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . '&page={page}' . $url, true);

        $this->data['pagination'] = $pagination->render();

        $this->data['delete'] = $this->url->link('billing/vendor/delete', 'token=' . $this->session->data['token'], true);
        $this->data['insert'] = $this->url->link('billing/vendor/form', 'token=' . $this->session->data['token'], true);

        $this->data['token'] = $this->session->data['token'];

        $this->data['success'] = $this->build->data('success', $this->session->data);
        $this->data['error_warning'] = $this->build->data('warning', $this->error);
        $this->data['selected'] = $this->build->data('selected', $this->request->post, array(), array());

        $url = $this->build->url(array(
            'filter_name',
            'filter_vendor_id',
            'filter_email',
            'filter_phone',
            'filter_date_added',
            'filter_date_modified'
        ));

        $this->data['sort'] = $sort;
        $this->data['order'] = $order;

        if ($order == 'ASC') {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $this->data['sort_name'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=name&order=' . $order, true);
        $this->data['sort_address'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=address&order=' . $order, true);
        $this->data['sort_email'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=email&order=' . $order, true);
        $this->data['sort_phone'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=phone&order=' . $order, true);
        $this->data['sort_date_added'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=date_added&order=' . $order, true);
        $this->data['sort_date_modified'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url . '&sort=date_modified&order=' . $order, true);

        $this->data['filter_name'] = $filter_name;
        $this->data['filter_address'] = $filter_address;
        $this->data['filter_email'] = $filter_email;
        $this->data['filter_phone'] = $filter_phone;
        $this->data['filter_date_added'] = $filter_date_added;
        $this->data['filter_date_modified'] = $filter_date_modified;

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->render('billing/vendor_list'));
    }

    public function delete() {
        $this->load->language('billing/vendor');

        $this->load->model('billing/vendor');

      if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $vendor_id) {
                $this->model_billing_vendor->deleteVendor($vendor_id);
               
            }

           $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('billing/vendor', 'token=' . $this->session->data['token'], true));
       }

        $this->index();
    }

    public function form() {
        $this->data = $this->load->language('billing/vendor');

        $this->document->setTitle($this->language->get('heading_title'));

        $url = $this->build->url(array(
            'filter_name',
            'filter_address',
            'filter_email',
            'filter_phone',
            'filter_date_added',
            'filter_date_modified',
            'sort',
            'order',
            'page',
            'vendor_id'
        ));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('billing/vendor', 'token=' . $this->session->data['token'], true)
        );

        $this->load->model('billing/vendor');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            if (isset($this->request->get['vendor_id'])) {
                $this->model_billing_vendor->editVendor((int)$this->request->get['vendor_id'], $this->request->post);
            } else {
                $this->model_billing_vendor->addVendor($this->request->post);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url, true));
        }

        if (isset($this->request->get['vendor_id'])) {
            $vendor_info = $this->model_billing_vendor->getVendor((int)$this->request->get['vendor_id']);
        } else {
            $vendor_info = array();
        }

        $this->data['action'] = $this->url->link('billing/vendor/form', 'token=' . $this->session->data['token'] . $url, true);

        $this->data['cancel'] = $this->url->link('billing/vendor', 'token=' . $this->session->data['token'] . $url, true);

        $this->data['token'] = $this->session->data['token'];

        $this->data['error_warning'] = $this->build->data('warning', $this->error);
        $this->data['error_name'] = $this->build->data('name', $this->error);
        $this->data['error_address'] = $this->build->data('address', $this->error);
        $this->data['error_email'] = $this->build->data('email', $this->error);
        $this->data['error_phone'] = $this->build->data('phone', $this->error);
     

        if (isset($this->request->get['vendor_id'])) {
            $this->data['vendor_id'] = (int)$this->request->get['vendor_id'];;
        } else {
            $this->data['vendor_id'] = false;
        }

        $this->data['name'] = $this->build->data('name', $this->request->post, $vendor_info);
        $this->data['address'] = $this->build->data('address', $this->request->post, $vendor_info);
        $this->data['email'] = $this->build->data('email', $this->request->post, $vendor_info);
        $this->data['phone'] = $this->build->data('phone', $this->request->post, $vendor_info);

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->render('billing/vendor_form'));
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_address']) || isset($this->request->get['filter_phone'])) {
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
             
            } else {
                $filter_name = '';
            }

            if (isset($this->request->get['filter_email'])) {
                $filter_email = $this->request->get['filter_email'];
            } else {
                $filter_email = '';
            }

            if (isset($this->request->get['filter_address'])) {
                $filter_address = $this->request->get['filter_address'];
            } else {
                $filter_address = '';
            }

             if (isset($this->request->get['filter_phone'])) {
                $filter_phone = $this->request->get['filter_phone'];
            } else {
                $filter_phone = '';
            }

            $filter_data = array(
                'filter_name'  => $filter_name,
                'filter_email' => $filter_email,
                'filter_phone' => $filter_phone,
                'filter_address' => $filter_address,
                'start'        => 0,
                'limit'        => $this->config->get('config_limit_admin')
            );

            $this->load->model('billing/vendor');

            $vendors = $this->model_billing_vendor->getVendors($filter_data);

            foreach ($vendors as $vendor) {
                $json[] = $vendor;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'billing/vendor')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

   

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'billing/vendor')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['name']) < 1) || (utf8_strlen($this->request->post['name']) > 30)) {
            $this->error['name'] = $this->language->get('error_name');
        }

        if ((utf8_strlen($this->request->post['address']) < 1) || (utf8_strlen($this->request->post['address']) > 100)) {
            $this->error['address'] = $this->language->get('error_address');
        }

        if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error['email'] = $this->language->get('error_email');
        }

         if ((utf8_strlen($this->request->post['phone']) > 15)) {
           $this->error['phone'] = $this->language->get('error_phone');
         }

         if((!preg_match("/^[0-9]*$/",$this->request->post['phone'])) )    {
             $this->error['phone'] = $this->language->get('error_phone');
         } else if(utf8_strlen($this->request->post['phone']) < 7) {
             $this->error['phone'] = $this->language->get('error_phone'); 
         }


        if ($this->error && empty($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_form');
        }

        return !$this->error;
    }
}
