<?php
require_once("report.php");
class Summary_discounts extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_discount_reason'),$this->lang->line('reports_count'));
	}
	
	public function getData(array $inputs)
	{
	
		$this->db->select('discount_reason, count(*) as count', false);
		$this->db->from('sales_items_temp');
		$this->db->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'" and discount > 0');
		$this->db->group_by('sales_items_temp.discount');
		$this->db->order_by('discount');
		return $this->db->get()->result_array();		
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->db->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax,sum(profit) as profit');
		$this->db->from('sales_items_temp');
		$this->db->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		return $this->db->get()->row_array();		
	}
}
?>