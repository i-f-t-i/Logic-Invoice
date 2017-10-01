<?php
defined('_PATH') or die('Restricted!');

class ModelBillingVendorInvoice extends Model {
    public function addInvoice($data) {
        if (empty($data['vendor_id'])) {
            $this->load->model('billing/vendor');

            if ($vendor_info = $this->model_billing_vendor->getVendorByEmail($data['email'])) {
                $data['vendor_id'] = $vendor_info['vendor_id'];
            } else {
                $data['status'] = 1;

                $data['vendor_id'] = $this->model_billing_vendor->addVendor($data);
            }
        }

        $this->db->query("INSERT INTO " . DB_PREFIX . "vendor_invoice SET vendor_id = '" . (int)$data['vendor_id'] . "', vendor_name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['payment_address']) . "', email = '" . $this->db->escape($data['payment_email']) . "', phone = '" . $this->db->escape($data['payment_phone']) . "', invoice_no = '" . $this->db->escape($data['invoiceno']) . "', due_date = '" . $this->db->escape($data['date_due']) . "', payment_method = '" . $this->db->escape($data['payment_code']) . "', payment_description = '" . $this->db->escape($data['payment_description']) . "', currency = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . $this->db->escape($data['currency_value']) . "', total = '" . (float)$data['total'] . "', date_issued = NOW(), date_modified = NOW()");

        $invoice_id = $this->db->getLastId();
		
		$this->load->model('accounting/inventory');

        foreach ($data['items'] as $item) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "vendor_item SET vendor_id = '" . (int)$invoice_id . "', inventory_id = '" . (int)$item['inventory_id'] . "', title = '" . $this->db->escape($item['title']) . "', description = '" . $this->db->escape($item['description']) . "', tax_class_id = '" . (int)$item['tax_class_id'] . "', quantity = '" . (int)$item['quantity'] . "', price = '" . (float)$item['price'] . "', tax = '" . (float)$item['tax'] . "', discount = '" . (float)$item['discount'] . "'");
        
			// Add inventory

			if ($this->config->get('config_auto_subtract_inventory')) {
				if ($item['inventory_id']) {
					$inventory_info = $this->model_accounting_inventory->getInventory($item['inventory_id']);
					
					if ($inventory_info) {
						$quantity = $inventory_info['quantity'] + $item['quantity'];
						
						$this->model_accounting_inventory->editInventoryData($item['inventory_id'], 'quantity', $quantity);
					}
				}
			}
		}

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "vendor_total SET vendor_invoice_id = '" . (int)$invoice_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', value = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
        }

        $invoice_info = $this->getInvoice($invoice_id);

        

       
    }

    public function editInvoice($invoice_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "vendor_invoice SET vendor_id = '" . (int)$data['vendor_id'] . "', vendor_name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['payment_address']) . "', email = '" . $this->db->escape($data['payment_email']) . "', phone = '" . $this->db->escape($data['payment_phone']) . "', invoice_no = '" . $this->db->escape($data['invoiceno']) . "', due_date = '" . $this->db->escape($data['date_due']) . "', payment_method = '" . $this->db->escape($data['payment_code']) . "', payment_description = '" . $this->db->escape($data['payment_description']) . "', currency = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . $this->db->escape($data['currency_value']) . "', transaction = '0', total = '" . (float)$data['total'] . "', date_modified = NOW() WHERE invoice_id = '" . (int)$invoice_id . "'");

		$this->load->model('accounting/inventory');
		
		// Restore inventory
		if ($this->config->get('config_auto_subtract_inventory')) {
			$item_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "invoice_item WHERE invoice_id = '" . (int)$invoice_id . "'");
			
			foreach ($item_query->rows as $item) {
				if ($item['inventory_id']) {
					$inventory_info = $this->model_accounting_inventory->getInventory($item['inventory_id']);
					
					if ($inventory_info) {
						$quantity = $inventory_info['quantity'] - $item['quantity'];
						
						$this->model_accounting_inventory->editInventoryData($item['inventory_id'], 'quantity', $quantity);
					}
				}
			}
		}
		
        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor_item WHERE vendor_id = '" . (int)$invoice_id . "'");

        foreach ($data['items'] as $item) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "vendor_item SET vendor_id = '" . (int)$invoice_id . "', inventory_id = '" . (int)$item['inventory_id'] . "', title = '" . $this->db->escape($item['title']) . "', description = '" . $this->db->escape($item['description']) . "', tax_class_id = '" . (int)$item['tax_class_id'] . "', quantity = '" . (int)$item['quantity'] . "', price = '" . (float)$item['price'] . "', tax = '" . (float)$item['tax'] . "', discount = '" . (float)$item['discount'] . "'");
        
			// Add inventory

			if ($this->config->get('config_auto_subtract_inventory')) {
				if ($item['inventory_id']) {
					$inventory_info = $this->model_accounting_inventory->getInventory($item['inventory_id']);
					
					if ($inventory_info) {
						$quantity = $inventory_info['quantity'] + $item['quantity'];
						
						$this->model_accounting_inventory->editInventoryData($item['inventory_id'], 'quantity', $quantity);
					}
				}
			}
		}

        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor_total WHERE vendor_invoice_id = '" . (int)$invoice_id . "'");

        foreach ($data['totals'] as $total) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "vendor_total SET vendor_invoice_id = '" . (int)$invoice_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', value = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
        }

        $invoice_info = $this->getInvoice($invoice_id);

        
    }

    public function deleteInvoice($invoice_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor_invoice WHERE invoice_id = '" . (int)$invoice_id . "'");
      
        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor_item WHERE vendor_id = '" . (int)$invoice_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor_total WHERE vendor_invoice_id = '" . (int)$invoice_id . "'");
    }

    public function getInvoice($invoice_id) {

        $query = $this->db->query("SELECT * FROM vendor_invoice WHERE invoice_id = '" . (int)$invoice_id . "'");

        if ($query->num_rows) {
            $invoice_item_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_item WHERE vendor_id = '" . (int)$query->row['invoice_id'] . "'");

            $items = array();

            foreach ($invoice_item_query->rows as $item) {
                $items[] = array(
                    'invoice_item_id'    => $item['vendor_item_id'],
                    'invoice_id'         => $item['vendor_id'],
                    'inventory_id'       => $item['inventory_id'],
                    'title'              => $item['title'],
                    'description'        => $item['description'],
                    'tax_class_id'       => $item['tax_class_id'],
                    'quantity'           => $item['quantity'],
                    'price'              => $item['price'],
                    'tax'                => $item['tax'],
                    'converted_price'    => round($item['price'] * $query->row['currency_value'], 4),
                    'discount'           => $item['discount'],
                    'converted_discount' => round($item['discount'] * $query->row['currency_value'], 4)
                );
            }



            $invoice_total_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_total WHERE vendor_invoice_id = '" . (int)$query->row['invoice_id'] . "' ORDER BY sort_order");

             $this->load->model('billing/vendor');

            $vendor_info = $this->model_billing_vendor->getVendor($query->row['vendor_id']);
               



            return array(
                'invoice_id'          => $query->row['invoice_id'],
                'vendor_id'         => $query->row['vendor_id'],
              'name'            => $query->row['vendor_name'],
              'email'            =>     $vendor_info['email'],
              'address'            =>     $vendor_info['address'],
              'phone'            =>     $vendor_info['phone'],
                'payment_address'           => $query->row['address'],
               'payment_email'            => $query->row['email'],
                'payment_phone'             => $query->row['phone'],
                'total'             => $query->row['total'],
                'date_due'               => $query->row['due_date'],
                'payment_code'   => $query->row['payment_method'],
                'payment_description'    => $query->row['payment_description'],
                'currency_code'     => $query->row['currency'],
                'currency_value'   => $query->row['currency_value'],
                'date_modified'   => $query->row['date_modified'],
                'invoiceno'        => $query->row['invoice_no'],
                'date_issued'        => $query->row['date_issued'],
                'items'               => $items,
                'totals'              => $invoice_total_query->rows
            );
        } else {
            return false;
        }
    }

    public function getInvoices($data = array()) {
      
          $sql = "SELECT * FROM vendor_invoice";

        $implode = array();

        if (!empty($data['filter_invoice_id'])) {
            $implode[] = "invoice_id = '" . (int)$data['filter_invoice_id'] . "'";
        }

       

        if (!empty($data['filter_name'])) {
            $implode[] = "vendor_name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_total'])) {
            $implode[] = "total = '" . (float)$data['filter_total'] . "'";
        }

      

        if (isset($data['filter_transaction']) && !is_null($data['filter_transaction'])) {
            $implode[] = "transaction = '" . (int)$data['filter_transaction'] . "'";
        }

        if (!empty($data['filter_date_due'])) {
            $implode[] = "DATE(date_due) = DATE('" . $this->db->escape($data['filter_date_due']) . "')";
        }

        if (!empty($data['filter_date_issued'])) {
            $implode[] = "DATE(date_issued) = DATE('" . $this->db->escape($data['filter_date_issued']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $implode[] = "DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'invoice_id',
            'name',
            'total',
           
            'date_issued',
            'date_due',
            'date_modified',
            'transaction'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY invoice_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) && isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalInvoices($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor_invoice";

        $implode = array();

        if (!empty($data['filter_invoice_id'])) {
            $implode[] = "invoice_id = '" . (int)$data['filter_invoice_id'] . "'";
        }

       

        if (!empty($data['filter_name'])) {
            $implode[] = "vendor_name AS name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_total'])) {
            $implode[] = "total = '" . (float)$data['filter_total'] . "'";
        }

      

        if (isset($data['filter_transaction']) && !is_null($data['filter_transaction'])) {
            $implode[] = "transaction = '" . (int)$data['filter_transaction'] . "'";
        }

        if (!empty($data['filter_date_due'])) {
            $implode[] = "DATE(date_due) = DATE('" . $this->db->escape($data['filter_date_due']) . "')";
        }

        if (!empty($data['filter_date_issued'])) {
            $implode[] = "DATE(date_issued) = DATE('" . $this->db->escape($data['filter_date_issued']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $implode[] = "DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }


}

