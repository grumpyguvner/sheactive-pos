<?php
require_once("export.php");
class Detailed_sales extends Export
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array($this->lang->line('headoffice_sale_id'), $this->lang->line('headoffice_date'), $this->lang->line('headoffice_item_plu'), $this->lang->line('headoffice_items_purchased'), $this->lang->line('headoffice_quantity_remaining')),
			     'details' => array()
		);		
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('sales.sale_id AS sale_id, sales.sale_time AS sale_date, sales_items.item_id AS item_id, sales_items.line AS line, items.item_number AS PLU, sales_items.quantity_purchased as items_purchased, items.quantity AS items_remaining', false);
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id', 'left');
		$this->db->join('items', 'sales_items.item_id = items.item_id', 'left');
		$this->db->where('sales_items.ho_update = "'. $inputs['ho_update']. '"');
		$this->db->order_by('sales.sale_id');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
		
//		foreach($data['summary'] as $key=>$value)
//		{
//			$this->db->select('name, category, quantity_purchased, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, discount');
//			$this->db->from('sales_items_temp');
//			$this->db->join('items', 'sales_items_temp.item_id = items.item_id');
//			$this->db->where('sale_id = '.$value['sale_id']);
//			$data['details'][$key] = $this->db->get()->result_array();
//		}
		
//		return $data;
		return $data['summary'];
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('sum(quantity_purchased) as items_purchased_total');
		$this->db->from('sales');
		$this->db->join('sales_items', 'sales.sale_id = sales_items.sale_id', 'left');
		$this->db->where('sales_items.ho_update = "'. $inputs['ho_update']. '"');
		
		return $this->db->get()->row_array();
	}
}
?>