<?php
require_once("report.php");
class Specific_supplier extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
//		return array('summary' => array($this->lang->line('reports_receiving_id'), $this->lang->line('reports_date'), $this->lang->line('reports_items_purchased'), $this->lang->line('reports_sold_by'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_payment_type'), $this->lang->line('reports_comments')),
//					'details' => array($this->lang->line('reports_name'), $this->lang->line('reports_category'),$this->lang->line('reports_serial_number'), $this->lang->line('reports_description'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_discount'))
//		);
		return array('summary' => array($this->lang->line('reports_item'), $this->lang->line('reports_name'), $this->lang->line('reports_description'), $this->lang->line('reports_supplierref'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_cost_price'), $this->lang->line('reports_total')),
                             'details' => array($this->lang->line('reports_receiving_id'), $this->lang->line('reports_date'), $this->lang->line('reports_items_purchased'), $this->lang->line('reports_employee'), $this->lang->line('reports_cost_price'), $this->lang->line('reports_total'), $this->lang->line('reports_comments'))
		);
	}
	
	public function getData(array $inputs)
	{
//		$this->db->select('receiving_id, receiving_date, sum(quantity_purchased) as items_purchased, CONCAT(first_name," ",last_name) as employee_name, sum(subtotal) as subtotal, sum(total) as total, payment_type, comment', false);
		$this->db->select('item_id, item_number, name, receivings_items_temp.description, supplierref, sum(quantity_purchased) as items_purchased, item_cost_price, sum(total) as total', false);
		$this->db->from('receivings_items_temp');
		$this->db->where('receiving_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'" and supplier_id='.$inputs['supplier_id']);
		$this->db->group_by('item_id');
		$this->db->order_by('item_number');

		$data = array();
		$data['summary'] = $this->db->get()->result_array();
		$data['details'] = array();
		
		foreach($data['summary'] as $key=>$value)
		{
			$this->db->select('receiving_id, receiving_date, quantity_purchased, CONCAT(first_name," ",last_name) as employee_name, item_cost_price,total, comment');
			$this->db->from('receivings_items_temp');
                        $this->db->join('people', 'receivings_items_temp.employee_id = people.person_id');
			$this->db->where('item_number = '.$value['item_number']);
			$data['details'][$key] = $this->db->get()->result_array();
		}
		
		return $data;
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total');
		$this->db->from('receivings_items_temp');
		$this->db->where('receiving_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'" and supplier_id='.$inputs['supplier_id']);
		
		return $this->db->get()->row_array();
	}
}
?>