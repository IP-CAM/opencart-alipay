<?php
class ModelPaymentAlipay extends Model {
	public function getMethod($address, $total) {
		$this->load->language('payment/alipay');
 
		$status = true;
		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'alipay',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('alipay_sort_order')
			);
		}

		return $method_data;
	}


 	public function addOrder($data) {
	    
		$this->db->query("INSERT INTO `" . DB_PREFIX . "alipay_order` SET `order_id` = '" . (int)$data['order_id'] . "', `type` = '" . $this->db->escape($data['type']) . "', `trade_no` = '" . $this->db->escape($data['trade_no']) . "',  `discount` = '" . $this->db->escape($data['discount']) . "', `payment_type` = '" . $this->db->escape($data['payment_type']) . "', `subject` = '" . $this->db->escape($data['subject']) . "',`buyer_email` = '" . $this->db->escape($data['buyer_email']) . "',`gmt_create` = '" . $this->db->escape($data['gmt_create']) . "',`notify_type` = '" . $this->db->escape($data['notify_type']) . "',`quantity` = '" . $this->db->escape($data['quantity']) . "',`seller_id` = '" . $this->db->escape($data['seller_id']) . "',`notify_time` = '" . $this->db->escape($data['notify_time']) . "',`body` = '" . $this->db->escape($data['body']) . "',`trade_status` = '" . $this->db->escape($data['trade_status']) . "',`is_total_fee_adjust` = '" . $this->db->escape($data['is_total_fee_adjust']) . "',`total_fee` = '" . $this->db->escape($data['total_fee']) . "',`gmt_payment` = '" . $this->db->escape($data['gmt_payment']) . "',`price` = '" . $this->db->escape($data['price']) . "',`buyer_id` = '" . $this->db->escape($data['buyer_id']) . "',`notify_id` = '" . $this->db->escape($data['notify_id']) . "',`use_coupon` = '" . $this->db->escape($data['use_coupon']) . "',`sign_type` = '" . $this->db->escape($data['sign_type']) . "',`sign` = '" . $this->db->escape($data['sign']) . "'");

		return $this->db->getLastId();
	}
   
	public function logger($message) {
		if ($this->config->get('alipay_debug') == 1) {
			$log = new Log('alipay.log');
			$log->write($message);
		}
	}


}
