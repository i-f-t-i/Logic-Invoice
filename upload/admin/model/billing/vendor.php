<?php
defined('_PATH') or die('Restricted!');

class ModelBillingVendor extends Model {
    public function addVendor($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "vendor SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', email = '" . $this->db->escape($data['email']) . "', phone = '" . $this->db->escape($data['phone']) . "', date_added = NOW(), date_modified = NOW()");

        $vendor_id = $this->db->getLastId();

        $vendor_info = $this->getVendor($vendor_id);

        return $vendor_id;
    }

    public function editVendor($vendor_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "vendor SET name = '" . $this->db->escape($data['name']) . "', address = '" . $this->db->escape($data['address']) . "', email = '" . $this->db->escape($data['email']) . "', phone = '" . $this->db->escape($data['phone']) . "', date_modified = NOW() WHERE vendor_id = '" . (int)$vendor_id . "'");

    }

    public function deleteVendor($vendor_id) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$vendor_id . "'");

        $this->load->model('billing/vendor');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor_invoice WHERE vendor_id = '" . (int)$vendor_id . "'");

        foreach ($query->rows as $result) {
            $this->model_billing_vendorinvoice->deleteInvoice($result['invoice_id']);
        }

    }

    public function getVendor($vendor_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vendor WHERE vendor_id = '" . (int)$vendor_id . "'");

        return $query->row;
    }


    public function getVendors($data = array()) {
      //  $sql = "SELECT *, name, date_added FROM " . DB_PREFIX . "vendor";

        $sql = "SELECT * FROM " . DB_PREFIX . "vendor";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

          if (!empty($data['filter_address'])) {
            $implode[] = "address LIKE '%" . $this->db->escape($data['filter_address']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }

          if (!empty($data['filter_phone'])) {
            $implode[] = "phone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
        }

        if (!empty($data['filter_date_modified'])) {
            $implode[] = "DATE(date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sort_data = array(
            'name',
            'address',
            'email',
            'phone',
            'date_added',
            'date_modified'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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

    public function getTotalVendors($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "vendor";

        $implode = array();

        if (!empty($data['filter_name'])) {
            $implode[] = "name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }

        if (!empty($data['filter_address'])) {
            $implode[] = "address LIKE '%" . $this->db->escape($data['filter_address']) . "%'";
        }

        if (!empty($data['filter_email'])) {
            $implode[] = "email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }

        if (!empty($data['filter_phone'])) {
            $implode[] = "phone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }

        if (!empty($data['filter_date_added'])) {
            $implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
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