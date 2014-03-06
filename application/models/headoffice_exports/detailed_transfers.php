<?php
require_once("export.php");
class Detailed_transfers extends Export
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array($this->lang->line('headoffice_trans_id'),
                                                $this->lang->line('headoffice_date'),
                                                $this->lang->line('headoffice_comment'),
                                                $this->lang->line('headoffice_item_plu'),
                                                $this->lang->line('headoffice_item_ean'),
                                                $this->lang->line('headoffice_item_name'),
                                                $this->lang->line('headoffice_item_description'),
                                                $this->lang->line('headoffice_quantity'),
                                                $this->lang->line('headoffice_cost_price'),
                                                $this->lang->line('headoffice_unit_price'),
                                                $this->lang->line('headoffice_company_name'),
                                                $this->lang->line('headoffice_supplierref')),
			     'details' => array()
		);		
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('t.transfer_id,
                                   t.transfer_time,
                                   t.comment,
                                   p.item_number,
                                   p.ean_upc,
                                   p.name,
                                   p.description,
                                   i.quantity_transfered,
                                   p.cost_price,
                                   p.unit_price,
                                   s.company_name,
                                   p.supplierref', false);
		$this->db->from('transfers_items i');
		$this->db->join('transfers t', 'i.transfer_id = t.transfer_id');
		$this->db->join('items p', 'i.item_id = p.item_id');
		$this->db->join('suppliers s', 'p.supplier_id = s.person_id');
		$this->db->where('t.branch_ref = "'. $inputs['branch_ref']. '"');
		$this->db->where('t.transfer_id = '. $inputs['transfer_id']);
		$this->db->order_by('t.transfer_time');
$this->db->limit(10);

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