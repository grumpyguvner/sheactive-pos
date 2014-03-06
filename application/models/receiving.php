<?php
class Receiving extends Model
{
	public function get_info($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}

	function exists($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function save ($items,$supplier_id,$employee_id,$comment,$payment_type,$receiving_id=false)
	{
		if(count($items)==0)
			return -1;

		$receivings_data = array(
		'supplier_id'=> $this->Supplier->exists($supplier_id) ? $supplier_id : null,
		'employee_id'=>$employee_id,
		'payment_type'=>$payment_type,
		'comment'=>$comment
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('receivings',$receivings_data);
		$receiving_id = $this->db->insert_id();


		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$receivings_items_data = array
			(
				'receiving_id'=>$receiving_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_purchased'=>$item['quantity'],
				'discount'=>$item['discount'],
				'discount_type'=>$item['discount_type'],
				'discount_reason'=>$item['discount_reason'],
				'item_cost_price' =>$item['cost_price'],
				'item_unit_price'=>$cur_item_info->unit_price,
				'location_id'=>$item['location_id']
			);

			$this->db->insert('receivings_items',$receivings_items_data);

			//Update stock quantity
			$item_data = array('quantity'=>$cur_item_info->quantity + $item['quantity']);
			$this->Item->save($item_data,$item['item_id']);
			
			$qty_recv = $item['quantity'];
			$recv_remarks ='RECV '.$receiving_id;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$recv_remarks,
				'trans_inventory'=>$qty_recv
			);
			$this->Inventory->insert($inv_data);

			$supplier = $this->Supplier->get_info($supplier_id);
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}


		return $receiving_id;
	}

	function get_receiving_items($receiving_id)
	{
		$this->db->from('receivings_items');
		$this->db->where('receiving_id',$receiving_id);
		return $this->db->get();
	}

	function get_supplier($receiving_id)
	{
		$this->db->from('receivings');
		$this->db->where('receiving_id',$receiving_id);
		return $this->Supplier->get_info($this->db->get()->row()->supplier_id);
	}


	//We create a temp table that allows us to do easy report/receivings queries
	public function create_receivings_items_temp_table()
	{
		$strSQL = "CREATE TEMPORARY TABLE ".$this->db->dbprefix('receivings_items_temp')."
		(SELECT p.item_number AS item_number, p.name AS name, date(r.receiving_time) as receiving_date, r.receiving_id AS receiving_id, r.comment AS comment, r.supplier_id AS supplier_id, r.employee_id AS employee_id,
		p.item_id AS item_id, p.supplierref AS supplierref, quantity_purchased, item_cost_price, item_unit_price,
		i.discount_reason, CONCAT(discount, discount_type) AS discount, IF(discount_type='%',((item_cost_price-(item_cost_price*discount/100))*quantity_purchased),((item_cost_price-discount)*quantity_purchased)) as subtotal,
		i.line as line, i.description as description,
		ROUND(IF(discount_type='%',((item_cost_price-(item_cost_price*discount/100))*quantity_purchased),((item_cost_price-discount)*quantity_purchased)),2) as total
		FROM ".$this->db->dbprefix('receivings_items')." i
		INNER JOIN ".$this->db->dbprefix('receivings')." r ON  i.receiving_id=r.receiving_id
		INNER JOIN ".$this->db->dbprefix('items')." p ON i.item_id=p.item_id
		ORDER BY p.item_id, i.receiving_id)";

                log_message('debug', $strSQL);
		$this->db->query($strSQL);

		//Update null subtotals to be equal to the total as these don't have tax
		$this->db->query('UPDATE '.$this->db->dbprefix('receivings_items_temp'). ' SET total=subtotal WHERE total IS NULL');
	}

}
?>