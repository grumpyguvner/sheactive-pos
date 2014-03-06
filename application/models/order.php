<?php
class Order extends Model
{
	public function get_info($order_id)
	{
		$this->db->from('orders');
		$this->db->where('order_id',$order_id);
		return $this->db->get();
	}

	function exists($order_id)
	{
		$this->db->from('orders');
		$this->db->where('order_id',$order_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function save ($items,$customer_id,$employee_id,$comment,$payments,$order_id=false)
	{
		if(count($items)==0)
			return -1;

		//Alain Multiple payments
		//Build payment types string
		$payment_types='';
		foreach($payments as $payment_id=>$payment)
		{
			$payment_types=$payment_types.$payment['payment_type'].': '.to_currency($payment['payment_amount']).'<br>';
		}

		$orders_data = array(
		'order_time' => date('Y-m-d H:i:s'),
		'customer_id'=> $this->Customer->exists($customer_id) ? $customer_id : null,
		'employee_id'=>$employee_id,
		'payment_type'=>$payment_types,
		'comment'=>$comment
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('orders',$orders_data);
		$order_id = $this->db->insert_id();

		foreach($payments as $payment_id=>$payment)
		{
			$orders_payments_data = array
			(
				'order_id'=>$order_id,
				'payment_type'=>$payment['payment_type'],
				'payment_amount'=>$payment['payment_amount']
			);
			$this->db->insert('orders_payments',$orders_payments_data);
		}

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$orders_items_data = array
			(
				'order_id'=>$order_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'discount'=>$item['discount'],
				'discount_type'=>$item['discount_type'],
				'discount_reason'=>$item['discount_reason'],
				'item_cost_price' => $cur_item_info->cost_price,
				'item_unit_price'=>$item['price']
			);

			$this->db->insert('orders_items',$orders_items_data);

			//Update stock quantity
			$item_data = array('quantity'=>$cur_item_info->quantity - $item['quantity']);
			$this->Item->save($item_data,$item['item_id']);
			
			//Ramel Inventory Tracking
			//Inventory Count Details
			$qty_buy = -$item['quantity'];
			$order_remarks ='POS '.$order_id;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$order_remarks,
				'trans_inventory'=>$qty_buy
			);
			$this->Inventory->insert($inv_data);
			//------------------------------------Ramel

			$customer = $this->Customer->get_info($customer_id);
 			if ($customer_id == -1 or $customer->taxable)
 			{
				foreach($this->Item_taxes->get_info($item['item_id']) as $row)
				{
					$this->db->insert('orders_items_taxes', array(
						'order_id' 	=>$order_id,
						'item_id' 	=>$item['item_id'],
						'line'      =>$item['line'],
						'name'		=>$row['name'],
						'percent' 	=>$row['percent']
					));
				}
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $order_id;
	}

	function get_order_items($order_id)
	{
		$this->db->from('orders_items');
		$this->db->where('order_id',$order_id);
		return $this->db->get();
	}

	function get_order_payments($order_id)
	{
		$this->db->from('orders_payments');
		$this->db->where('order_id',$order_id);
		return $this->db->get();
	}

	function get_customer($order_id)
	{
		$this->db->from('orders');
		$this->db->where('order_id',$order_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}

	//We create a temp table that allows us to do easy report/orders queries
	public function create_orders_items_temp_table()
	{
		$strSQL = "CREATE TEMPORARY TABLE ".$this->db->dbprefix('orders_items_temp')."
		(SELECT date(order_time) as order_date, ".$this->db->dbprefix('orders_items').".order_id, comment,payment_type, customer_id, employee_id,
		".$this->db->dbprefix('items').".item_id, supplier_id, quantity_purchased, item_cost_price, item_unit_price, SUM(percent) as item_tax_percent,
		discount_reason, CONCAT(discount, discount_type) AS discount, 
                ROUND(IF(discount_type='%',((item_unit_price-ROUND((item_unit_price*discount/100),2))*quantity_purchased),(ROUND((item_unit_price-discount),2)*quantity_purchased)),2) as subtotal,
		".$this->db->dbprefix('orders_items').".line as line, serialnumber, ".$this->db->dbprefix('orders_items').".description as description,
		ROUND(IF(discount_type='%',((item_unit_price-ROUND((item_unit_price*discount/100),2))*quantity_purchased),(ROUND((item_unit_price-discount),2)*quantity_purchased))*(1+(SUM(percent)/100)),2) as total,
		ROUND(IF(discount_type='%',((item_unit_price-ROUND((item_unit_price*discount/100),2))*quantity_purchased),(ROUND((item_unit_price-discount),2)*quantity_purchased))*(SUM(percent)/100),2) as tax,
		IF(discount_type='%',((item_unit_price-ROUND((item_unit_price*discount/100),2))*quantity_purchased),(ROUND((item_unit_price-discount),2)*quantity_purchased)) - (item_cost_price*quantity_purchased) as profit
		FROM ".$this->db->dbprefix('orders_items')."
		INNER JOIN ".$this->db->dbprefix('orders')." ON  ".$this->db->dbprefix('orders_items').'.order_id='.$this->db->dbprefix('orders').'.order_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('orders_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('suppliers')." ON  ".$this->db->dbprefix('items').'.supplier_id='.$this->db->dbprefix('suppliers').'.person_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('orders_items_taxes')." ON  "
		.$this->db->dbprefix('orders_items').'.order_id='.$this->db->dbprefix('orders_items_taxes').'.order_id'." and "
		.$this->db->dbprefix('orders_items').'.item_id='.$this->db->dbprefix('orders_items_taxes').'.item_id'." and "
		.$this->db->dbprefix('orders_items').'.line='.$this->db->dbprefix('orders_items_taxes').'.line'."
		GROUP BY order_id, item_id, line)";

                log_message('debug', $strSQL);
		$this->db->query($strSQL);

		//Update null item_tax_percents to be 0 instead of null
		$this->db->where('item_tax_percent IS NULL');
		$this->db->update('orders_items_temp', array('item_tax_percent' => 0));

		//Update null tax to be 0 instead of null
		$this->db->where('tax IS NULL');
		$this->db->update('orders_items_temp', array('tax' => 0));

		//Update null subtotals to be equal to the total as these don't have tax
		$this->db->query('UPDATE '.$this->db->dbprefix('orders_items_temp'). ' SET total=subtotal WHERE total IS NULL');
	}
}
?>
