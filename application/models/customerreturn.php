<?php
class Customerreturn extends Model
{
	public function get_info($customerreturn_id)
	{
		$this->db->from('customerreturns');
		$this->db->where('customerreturn_id',$customerreturn_id);
		return $this->db->get();
	}

	function exists($customerreturn_id)
	{
		$this->db->from('customerreturns');
		$this->db->where('customerreturn_id',$customerreturn_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function save ($items,$orderref,$customer_id,$employee_id,$comment,$customerreturn_id=false)
	{
		if(count($items)==0)
			return -1;

		$customerreturns_data = array(
		'customerreturn_time' => date('Y-m-d H:i:s'),
		'orderref'=>$orderref,
		'customer_id'=> $this->Customer->exists($customer_id) ? $customer_id : null,
		'employee_id'=>$employee_id,
		'comment'=>$comment
		);
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('customerreturns',$customerreturns_data);
		$customerreturn_id = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$customerreturns_items_data = array
			(
				'customerreturn_id'=>$customerreturn_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_returned'=>$item['quantity'],
				'reason_code'=>$item['reason_code'],
				'restock'=>$item['restock'],
				'faulty'=>$item['faulty'],
				'comment'=>$item['comment'],
				'item_cost_price' => $item['cost_price'],
				'item_unit_price'=>$item['price']
			);

			$this->db->insert('customerreturns_items',$customerreturns_items_data);

			//Update stock quantity
			$item_data = array('quantity'=>$cur_item_info->quantity - $item['quantity']);
			$this->Item->save($item_data,$item['item_id']);
			
			//Inventory Count Details
			If ($item['restock']==1)
			{
				//Update stock quantity
				$item_data = array('quantity'=>$cur_item_info->quantity + $item['quantity']);
				$this->Item->save($item_data,$item['item_id']);
			
				$qty_buy = $item['quantity'];
				$customerreturn_remarks ='RTN '.$customerreturn_id;
				$inv_data = array
				(
					'trans_date'=>date('Y-m-d H:i:s'),
					'trans_items'=>$item['item_id'],
					'trans_user'=>$employee_id,
					'trans_comment'=>$customerreturn_remarks,
					'trans_inventory'=>$qty_buy
				);
				$this->Inventory->insert($inv_data);
			}
			//------------------------------------Ramel

		}

		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}
		
		return $customerreturn_id;
	}

	function get_customerreturn_items($customerreturn_id)
	{
		$this->db->from('customerreturns_items');
		$this->db->where('customerreturn_id',$customerreturn_id);
		return $this->db->get();
	}

	function get_customer($customerreturn_id)
	{
		$this->db->from('customerreturns');
		$this->db->where('customerreturn_id',$customerreturn_id);
		return $this->Customer->get_info($this->db->get()->row()->customer_id);
	}
	
	//We create a temp table that allows us to do easy report/customerreturns queries
	public function create_customerreturns_items_temp_table()
	{
		$strSQL = "CREATE TEMPORARY TABLE ".$this->db->dbprefix('customerreturns_items_temp')."
		(SELECT date(customerreturn_time) as customerreturn_date, ".$this->db->dbprefix('customerreturns_items').".customerreturn_id, comment, customer_id, employee_id, 
		".$this->db->dbprefix('items').".item_id, supplier_id, quantity_returned, item_cost_price, item_unit_price, 
		comment, reason_code, IF(restock=0,'No','Yes') AS restock, IF(faulty=0,'No','Yes') AS faulty, ((item_cost_price)*quantity_returned)) as subtotal,
		".$this->db->dbprefix('customerreturns_items').".line as line, serialnumber, ".$this->db->dbprefix('customerreturns_items').".description as description,
		ROUND((item_unit_price)*quantity_returned) as total
		FROM ".$this->db->dbprefix('customerreturns_items')."
		INNER JOIN ".$this->db->dbprefix('customerreturns')." ON  ".$this->db->dbprefix('customerreturns_items').'.customerreturn_id='.$this->db->dbprefix('customerreturns').'.customerreturn_id'."
		INNER JOIN ".$this->db->dbprefix('items')." ON  ".$this->db->dbprefix('customerreturns_items').'.item_id='.$this->db->dbprefix('items').'.item_id'."
		LEFT OUTER JOIN ".$this->db->dbprefix('suppliers')." ON  ".$this->db->dbprefix('items').'.supplier_id='.$this->db->dbprefix('suppliers').'.person_id'."
		GROUP BY customerreturn_id, item_id, line)";

                log_message('debug', $strSQL);
		$this->db->query($strSQL);

		//Update null subtotals to be equal to the total as these don't have tax
		$this->db->query('UPDATE '.$this->db->dbprefix('customerreturns_items_temp'). ' SET total=subtotal WHERE total IS NULL');
	}
}
?>
