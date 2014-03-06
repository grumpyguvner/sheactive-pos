<?php
require_once ("secure_area.php");
class Stocktakes extends Secure_area
{
	function __construct()
	{
		parent::__construct('stocktakes');
		$this->load->library('stocktake_lib');
	}

	function index()
	{
                $this->_reload();
	}

	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function location_search()
	{
		$suggestions = $this->Location->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_location()
	{
		$location_id = $this->input->post("location");
		$this->stocktake_lib->set_location($location_id);
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$item_id_or_number_or_receipt = $this->input->post("item");
		$quantity = 1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                if(!$this->stocktake_lib->add_item($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('stock_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function add_plu()
	{
		$data=array();
		$item_id_or_number_or_receipt = $this->input->post("plu");
		$quantity = 1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                if(!$this->stocktake_lib->add_plu($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('stock_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function edit_item($item_id)
	{
		$data= array();

		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|integer');

                $description = $this->input->post("description");
                $ean_upc = $this->input->post("ean_upc");
                $serialnumber = $this->input->post("serialnumber");
		$quantity = $this->input->post("quantity");

		if ($this->form_validation->run() != FALSE)
		{
			$this->stocktake_lib->edit_item($item_id,$description,$ean_upc,$serialnumber,$quantity);
		}
		else
		{
			$data['error']=$this->lang->line('stock_error_editing_item');
		}

		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->stocktake_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_location()
	{
		$this->stocktake_lib->delete_location();
		$this->_reload();
	}

	function capture_ean($line_number)
	{
		$items = $this->stocktake_lib->get_cart();
                $item = $items[$line_number];

		$data['line'] = $line_number;
		$data['item_id'] = $item['item_id'];
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['quantity'] = $item['quantity'];
		$this->load->view("stocktakes/capture_ean",$data);
	}

	function complete()
	{
		$this->form_validation->set_rules('location_id', 'lang:stock_location', 'required');
		if ($this->form_validation->run() == FALSE)
		{
                        log_message('debug', 'Stocktake: Form validation failed');
		}

		$data['cart']=$this->stocktake_lib->get_cart();
		$data['receipt_title']=$this->lang->line('stock_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$location_id=$this->stocktake_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($location_id==-1)
                {
                    $data['error']=$this->lang->line('stock_error_location_required');
                    $this->_reload($data);

                } else {
                    $location_info=$this->Location->get_info($location_id);
                    $data['location']=$location_info->location_id;
                    //SAVE stocktake to database
                    $data['stocktake_id']='STOCK '.$this->Stocktake->save($data['cart'], $location_id,$employee_id,$comment);

                    if ($data['stocktake_id'] == 'STOCK -1')
                    {
                            $data['error_message'] = $this->lang->line('stocktakes_transaction_failed');
                    }

                    $this->load->view("stocktakes/receipt",$data);
                    $this->stocktake_lib->clear_all();

                }

	}

	function labels($stocktake_id)
	{
		$stocktake_info = $this->Stocktake->get_info($stocktake_id)->row_array();
		$this->stocktake_lib->copy_entire_stocktake($stocktake_id);
		$data['cart']=$this->stocktake_lib->get_cart();
		$data['receipt_title']=$this->lang->line('stock_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($stocktake_info['stocktake_time']));
		$location_id=$this->stocktake_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($location_id!=-1)
		{
			$location_info=$this->Location->get_info($location_id);
			$data['location']=$location_info->location_id;
		}
		$data['stocktake_id']='STOCK '.$stocktake_id;

		$this->load->view("stocktakes/labels",$data);
		$this->stocktake_lib->clear_all();

	}

	function receipt($stocktake_id)
	{
		$stocktake_info = $this->Stocktake->get_info($stocktake_id)->row_array();
		$this->stocktake_lib->copy_entire_stocktake($stocktake_id);
		$data['cart']=$this->stocktake_lib->get_cart();
		$data['receipt_title']=$this->lang->line('stock_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($stocktake_info['stocktake_time']));
		$location_id=$this->stocktake_lib->get_location();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($location_id!=-1)
		{
			$location_info=$this->Location->get_info($location_id);
			$data['location']=$location_info->location_id;
		}
		$data['stocktake_id']='STOCK '.$stocktake_id;
		$this->load->view("stocktakes/receipt",$data);
		$this->stocktake_lib->clear_all();

	}

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['cart']=$this->stocktake_lib->get_cart();
		$data['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		$data['locations_module_allowed'] = $this->Employee->has_permission('locations', $person_info->person_id);

		$location_id=$this->stocktake_lib->get_location();
		if($location_id!=-1)
		{
			$location_info=$this->Location->get_info($location_id);
			$data['location_id']=$location_id;
			$data['location']=$location_info->location_ref;
		}
		$this->load->view("stocktakes/stocktake",$data);
	}

    function cancel_stocktake()
    {
        //$this->load->view("stocktakes/receipt",$data);
    	$this->stocktake_lib->clear_all();
    	$this->_reload();

    }

}
?>