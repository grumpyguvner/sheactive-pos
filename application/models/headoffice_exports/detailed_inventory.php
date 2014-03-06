<?php
require_once("export.php");
class Detailed_inventory extends Export
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array($this->lang->line('headoffice_trans_id'), $this->lang->line('headoffice_date'), $this->lang->line('headoffice_item_plu'), $this->lang->line('headoffice_quantity'), $this->lang->line('headoffice_quantity_remaining'), $this->lang->line('headoffice_location')),
			     'details' => array()
		);		
	}
	
	public function getData(array $inputs, $incErrors = false)
	{
		$this->db->select('i.trans_id AS trans_id, i.trans_date AS trans_date, p.item_id AS item_id, p.item_number AS PLU, i.trans_inventory as quantity, p.quantity AS items_remaining, l.location_ref as location', false);
		$this->db->from('inventory i');
		$this->db->join('items p', 'i.trans_items = p.item_id', 'left');
		$this->db->join('locations l', 'p.location_id = l.location_id', 'left');
		$this->db->where('i.ho_update = "'. $inputs['ho_update']. '"');
                if (!$incErrors)
                    $this->db->where('i.ho_error');
		$this->db->order_by('i.trans_id');
//$this->db->limit(500);

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
		
//		foreach($data['summary'] as $key=>$value)
//		{
//			$this->db->select('name, category, quantity_purchased, serialnumber, trans_items_temp.description, subtotal,total, tax, profit, discount');
//			$this->db->from('trans_items_temp');
//			$this->db->join('items', 'trans_items_temp.item_id = items.item_id');
//			$this->db->where('tran_id = '.$value['tran_id']);
//			$data['details'][$key] = $this->db->get()->result_array();
//		}

//		return $data;
		return $data['summary'];
	}

	public function getTxErrors(array $inputs)
	{
		$this->db->select('i.trans_id AS trans_id, i.trans_date AS trans_date, p.item_id AS item_id, p.item_number AS PLU, i.trans_inventory as quantity, p.quantity AS items_remaining, i.ho_error', false);
		$this->db->from('inventory i');
		$this->db->join('items p', 'i.trans_items = p.item_id', 'left');
                foreach ($inputs as $key => $value) {
                    $this->db->where('i.'.$key.' = "'.$value.'"');
                }
                $this->db->where('i.ho_error > 0 AND i.ho_error < 3');
		$this->db->order_by('i.trans_id');
//$this->db->limit(500);

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();

//		foreach($data['summary'] as $key=>$value)
//		{
//			$this->db->select('name, category, quantity_purchased, serialnumber, trans_items_temp.description, subtotal,total, tax, profit, discount');
//			$this->db->from('trans_items_temp');
//			$this->db->join('items', 'trans_items_temp.item_id = items.item_id');
//			$this->db->where('tran_id = '.$value['tran_id']);
//			$data['details'][$key] = $this->db->get()->result_array();
//		}

//		return $data;
		return $data['summary'];
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('sum(i.trans_inventory) as quantity_total');
		$this->db->from('inventory i');
		$this->db->where('i.ho_update = "'. $inputs['ho_update']. '"');
		
		return $this->db->get()->row_array();
	}
}
?>
