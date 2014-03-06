<?php
require_once("report.php");
class Inventory_low extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_supplier'),
                             $this->lang->line('reports_supplierref'), 
                             $this->lang->line('reports_item_name'),
                             $this->lang->line('reports_item_number'),
                             $this->lang->line('reports_description'),
                             $this->lang->line('reports_count'),
                             $this->lang->line('reports_reorder_level'),
                             $this->lang->line('reports_reorder_quantity'));
	}
	
	public function getData(array $inputs)
	{
		$this->db->select('suppliers.company_name AS supplier, items.supplierref AS supplierref, items.name AS name, items.item_number AS item_number, items.quantity AS quantity, items.reorder_level AS reorder_level, items.reorder_quantity AS reorder_quantity, items.description AS description');
		$this->db->from('items');
		$this->db->join('suppliers', 'suppliers.person_id = items.supplier_id');
		$this->db->where('items.reorder_level > 0');
		$this->db->where('items.quantity <= pos_items.reorder_level');
		$this->db->where('items.deleted = 0');
		$this->db->order_by('items.name');
		
		return $this->db->get()->result_array();

	}
	
	public function getSummaryData(array $inputs)
	{
		return array();
	}
}
?>