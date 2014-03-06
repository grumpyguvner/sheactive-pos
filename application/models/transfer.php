<?php
class Transfer extends Model
{
	public function get_info($transfer_id)
	{
		$this->db->from('transfers');
		$this->db->where('transfer_id',$transfer_id);
		return $this->db->get();
	}

	function exists($transfer_id)
	{
		$this->db->from('transfers');
		$this->db->where('transfer_id',$transfer_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function save ($items,$branch_ref,$employee_id,$comment,$transfer_id=false)
	{
		if(count($items)==0)
			return -1;

		$transfers_data = array(
		'branch_ref'=> $this->Branch->exists($branch_ref) ? $branch_ref : null,
		'employee_id'=>$employee_id,
		'comment'=>$comment
		);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('transfers',$transfers_data);
		$transfer_id = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$transfers_items_data = array
			(
				'transfer_id'=>$transfer_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_transfered'=>$item['quantity']
			);

			$this->db->insert('transfers_items',$transfers_items_data);

			//Update stock quantity
			$item_data = array('quantity'=>$cur_item_info->quantity + $item['quantity']);
			$this->Item->save($item_data,$item['item_id']);
			
			$qty_tran = $item['quantity'];
			$tran_remarks ='TRAN '.$transfer_id.' to '.$branch_ref;
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$tran_remarks,
				'trans_inventory'=>$qty_tran
			);
			$this->Inventory->insert($inv_data);

			$branch = $this->Branch->get_info($branch_ref);
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}


		return $transfer_id;
	}

	function get_transfer_items($transfer_id)
	{
		$this->db->from('transfers_items');
		$this->db->where('transfer_id',$transfer_id);
		return $this->db->get();
	}

	function get_branch($transfer_id)
	{
		$this->db->from('transfers');
		$this->db->where('transfer_id',$transfer_id);
		return $this->Branch->get_info($this->db->get()->row()->branch_ref);
	}

}
?>
