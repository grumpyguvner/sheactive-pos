<?php
class Stocktake extends Model
{
	public function get_info($stocktake_id)
	{
		$this->db->from('stocktakes');
		$this->db->where('stocktake_id',$stocktake_id);
		return $this->db->get();
	}

	function exists($stocktake_id)
	{
		$this->db->from('stocktakes');
		$this->db->where('stocktake_id',$stocktake_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	function save ($items,$location_id,$employee_id,$comment,$stocktake_id=false)
	{
		if(count($items)==0)
			return -1;

		$stocktakes_data = array(
		'location_id'=> $this->Location->exists($location_id) ? $location_id : null,
		'employee_id'=>$employee_id,
		'comment'=>$comment
		);

		$location = $this->Location->get_info($location_id);

		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();

		$this->db->insert('stocktakes',$stocktakes_data);
		$stocktake_id = $this->db->insert_id();

		foreach($items as $line=>$item)
		{
			$cur_item_info = $this->Item->get_info($item['item_id']);

			$stocktakes_items_data = array
			(
				'stocktake_id'=>$stocktake_id,
				'item_id'=>$item['item_id'],
				'line'=>$item['line'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'quantity_counted'=>$item['quantity']
			);

			$this->db->insert('stocktakes_items',$stocktakes_items_data);

			//Update stock quantity
			$item_data = array(
                            'quantity'=>$item['quantity'],
                            'location_id'=>$location->location_id
                                );
			$this->Item->save($item_data,$item['item_id']);
			
			$qty_adjust = $item['quantity']-$cur_item_info->quantity;
			$tran_remarks ='STOCK '.$stocktake_id.' found '.$item['quantity'].' items in '.$location->location_ref.'.';
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item['item_id'],
				'trans_user'=>$employee_id,
				'trans_comment'=>$tran_remarks,
				'trans_inventory'=>$qty_adjust
			);
			$this->Inventory->insert($inv_data);
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			return -1;
		}


		return $stocktake_id;
	}

	function get_stocktake_items($stocktake_id)
	{
		$this->db->from('stocktakes_items');
		$this->db->where('stocktake_id',$stocktake_id);
		return $this->db->get();
	}

	function get_location($stocktake_id)
	{
		$this->db->from('stocktakes');
		$this->db->where('stocktake_id',$stocktake_id);
		return $this->Location->get_info($this->db->get()->row()->location_id);
	}

}
?>
