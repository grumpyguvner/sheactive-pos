<?php
require_once("report.php");
class Inventory_valuation extends Report
{
	function __construct()
	{
            parent::__construct();
	}
	
	public function getDataColumns()
	{
            return array($this->lang->line('reports_item_name'), $this->lang->line('reports_item_number'), $this->lang->line('reports_description'), $this->lang->line('reports_count'), $this->lang->line('reports_cost_price'));
	}
	
	public function getData(array $inputs)
	{
            $this->db->select('name, item_number, description, quantity, cost_price, (quantity*cost_price) as value');
            $this->db->from('items');
            $this->db->where('deleted', 0);
            $this->db->where('quantity <> 0');
            $this->db->order_by('name');

            return $this->db->get()->result_array();

	}
	
	public function getSummaryData(array $inputs)
	{
            return array();
	}
}
?>