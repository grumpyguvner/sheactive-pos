<?php
require_once ("secure_area.php");
class Transfers extends Secure_area
{
	function __construct()
	{
		parent::__construct('transfers');
		$this->load->library('transfer_lib');
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

	function branch_search()
	{
//log_message('debug', 'TRANSFER / BRANCH SEARCH');
		$suggestions = $this->Branch->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_branch()
	{
		$branch_ref = $this->input->post("branch");
		$this->transfer_lib->set_branch($branch_ref);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
		$this->transfer_lib->set_mode($mode);
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$mode = $this->transfer_lib->get_mode();
		$item_id_or_number_or_receipt = $this->input->post("item");
		$quantity = $mode=="receive" ? 1:-1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

		if($this->transfer_lib->is_valid_receipt($item_id_or_number_or_receipt) && $mode=='receive')
		{
			$this->transfer_lib->return_entire_transfer($item_id_or_number_or_receipt);
		}
		elseif(!$this->transfer_lib->add_item($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('trans_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function add_plu()
	{
		$data=array();
		$mode = $this->transfer_lib->get_mode();
		$item_id_or_number_or_receipt = $this->input->post("plu");
		$quantity = $mode=="receive" ? 1:-1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

		if($this->transfer_lib->is_valid_receipt($item_id_or_number_or_receipt) && $mode=='receive')
		{
			$this->transfer_lib->return_entire_transfer($item_id_or_number_or_receipt);
		}
		elseif(!$this->transfer_lib->add_plu($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('trans_unable_to_add_item');
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
			$this->transfer_lib->edit_item($item_id,$description,$ean_upc,$serialnumber,$quantity);
		}
		else
		{
			$data['error']=$this->lang->line('trans_error_editing_item');
		}

		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->transfer_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_branch()
	{
		$this->transfer_lib->delete_branch();
		$this->_reload();
	}

	function capture_ean($line_number)
	{
		$items = $this->transfer_lib->get_cart();
                $item = $items[$line_number];

		$data['line'] = $line_number;
		$data['item_id'] = $item['item_id'];
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['quantity'] = $item['quantity'];
		$this->load->view("transfers/capture_ean",$data);
	}

	function complete()
	{
                log_message('debug', 'Transfer: Complete start');

		$this->form_validation->set_rules('branch_ref', 'lang:trans_branch', 'required');
                log_message('debug', 'Transfer: Checking required fields');
		if ($this->form_validation->run() == FALSE)
		{
                        log_message('debug', 'Transfer: Form validation failed');
		}

                log_message('debug', 'Transfer: Form validation OK');
		$data['cart']=$this->transfer_lib->get_cart();
		$data['receipt_title']=$this->lang->line('trans_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$branch_ref=$this->transfer_lib->get_branch();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($branch_ref==-1)
                {
                    $data['error']=$this->lang->line('trans_error_branch_required');
                    $this->_reload($data);

                } else {
                    $branch_info=$this->Branch->get_info($branch_ref);
                    $data['branch']=$branch_info->branch_ref;
                    //SAVE transfer to database
                    $data['transfer_id']='TRAN '.$this->Transfer->save($data['cart'], $branch_ref,$employee_id,$comment);

                    if ($data['transfer_id'] == 'TRAN -1')
                    {
                            $data['error_message'] = $this->lang->line('transfers_transaction_failed');
                    }

                    $this->load->view("transfers/receipt",$data);
                    $this->transfer_lib->clear_all();

                }

	}

	function labels($transfer_id)
	{
		$transfer_info = $this->Transfer->get_info($transfer_id)->row_array();
		$this->transfer_lib->copy_entire_transfer($transfer_id);
		$data['cart']=$this->transfer_lib->get_cart();
		$data['receipt_title']=$this->lang->line('trans_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($transfer_info['transfer_time']));
		$branch_ref=$this->transfer_lib->get_branch();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($branch_ref!=-1)
		{
			$branch_info=$this->Branch->get_info($branch_ref);
			$data['branch']=$branch_info->branch_ref;
		}
		$data['transfer_id']='TRAN '.$transfer_id;

		$this->load->view("transfers/labels",$data);
		$this->transfer_lib->clear_all();

	}

	function receipt($transfer_id)
	{
		$transfer_info = $this->Transfer->get_info($transfer_id)->row_array();
		$this->transfer_lib->copy_entire_transfer($transfer_id);
		$data['cart']=$this->transfer_lib->get_cart();
		$data['receipt_title']=$this->lang->line('trans_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($transfer_info['transfer_time']));
		$branch_ref=$this->transfer_lib->get_branch();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($branch_ref!=-1)
		{
			$branch_info=$this->Branch->get_info($branch_ref);
			$data['branch']=$branch_info->branch_ref;
		}
		$data['transfer_id']='TRAN '.$transfer_id;
		$this->load->view("transfers/receipt",$data);
		$this->transfer_lib->clear_all();

	}

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['cart']=$this->transfer_lib->get_cart();
		$data['modes']=array('receive'=>$this->lang->line('trans_receive'),'return'=>$this->lang->line('trans_return'));
		$data['mode']=$this->transfer_lib->get_mode();
		$data['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		$data['branches_module_allowed'] = $this->Employee->has_permission('branches', $person_info->person_id);

		$branch_ref=$this->transfer_lib->get_branch();
		if($branch_ref!=-1)
		{
			$branch_info=$this->Branch->get_info($branch_ref);
			$data['branch']=$branch_info->branch_ref;
		}
		$this->load->view("transfers/transfer",$data);
	}

    function cancel_transfer()
    {
        //$this->load->view("transfers/receipt",$data);
    	$this->transfer_lib->clear_all();
    	$this->_reload();

    }

}
?>