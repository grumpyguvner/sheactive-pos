<?php
require_once ("secure_area.php");
class Receivings extends Secure_area
{
	function __construct()
	{
		parent::__construct('receivings');
		$this->load->library('receiving_lib');
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

	function supplier_search()
	{
		$suggestions = $this->Supplier->get_suppliers_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_supplier()
	{
		$supplier_id = $this->input->post("supplier");
		$this->receiving_lib->set_supplier($supplier_id);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
		$this->receiving_lib->set_mode($mode);
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$mode = $this->receiving_lib->get_mode();
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

                if($this->receiving_lib->is_valid_receipt($item_id_or_number_or_receipt) && $mode=='return')
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_receipt);
		}
		elseif(!$this->receiving_lib->add_item($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('recvs_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function add_plu()
	{
		$data=array();
		$mode = $this->receiving_lib->get_mode();
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

                if($this->receiving_lib->is_valid_receipt($item_id_or_number_or_receipt) && $mode=='return')
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_receipt);
		}
		elseif(!$this->receiving_lib->add_plu($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('recvs_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function edit_item($item_id)
	{
		$data= array();

		$this->form_validation->set_rules('cost_price', 'lang:cost_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|integer');
		$this->form_validation->set_rules('discount', 'lang:items_discount', 'required|integer');


                $description = $this->input->post("description");
                $ean_upc = $this->input->post("ean_upc");
                $serialnumber = $this->input->post("serialnumber");
		$cost_price = $this->input->post("cost_price");
		$quantity = $this->input->post("quantity");
		$discount = $this->input->post("discount");
                $discount_type = $this->input->post("discount_type");
                $discount_reason = $this->input->post("discount_reason");
                $location_id = $this->input->post("location_id");

		if ($this->form_validation->run() != FALSE)
		{
			$this->receiving_lib->edit_item($item_id,$description,$ean_upc,$serialnumber,$quantity,$discount,$discount_type,$discount_reason,$cost_price,$location_id);
		}
		else
		{
			$data['error']=$this->lang->line('recvs_error_editing_item');
		}

		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->receiving_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_supplier()
	{
		$this->receiving_lib->delete_supplier();
		$this->_reload();
	}

	function capture_ean($line_number)
	{
		$items = $this->receiving_lib->get_cart();
                $item = $items[$line_number];

		$data['line'] = $line_number;
		$data['item_id'] = $item['item_id'];
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['cost_price'] = $item['cost_price'];
		$data['quantity'] = $item['quantity'];
		$data['discount'] = $item['discount'];
		$data['discount_type'] = $item['discount_type'];
		$data['discount_reason'] = $item['discount_reason'];
		$data['location_id'] = $item['location_id'];
		$this->load->view("receivings/capture_ean",$data);
	}

	function complete()
	{
                log_message('debug', 'Receiving: Complete start');

		$this->form_validation->set_rules('supplier_id', 'lang:recvs_supplier', 'required');
                log_message('debug', 'Receiving: Checking required fields');
		if ($this->form_validation->run() == FALSE)
		{
                        log_message('debug', 'Receiving: Form validation failed');
		}

                log_message('debug', 'Receiving: Form validation OK');
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=$this->lang->line('recvs_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$supplier_id=$this->receiving_lib->get_supplier();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');

		if ($this->input->post('amount_tendered'))
		{
			$data['amount_tendered'] = $this->input->post('amount_tendered');
			$data['amount_change'] = to_currency($data['amount_tendered'] - round($data['total'], 2));
		}
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id==-1)
                {
                    $data['error']=$this->lang->line('recvs_error_supplier_required');
                    $this->_reload($data);

                } else {
                    $suppl_info=$this->Supplier->get_info($supplier_id);
                    $data['supplier']=$suppl_info->first_name.' '.$suppl_info->last_name;
                    //SAVE receiving to database
                    $data['receiving_id']='RECV '.$this->Receiving->save($data['cart'], $supplier_id,$employee_id,$comment,$payment_type);

                    if ($data['receiving_id'] == 'RECV -1')
                    {
                            $data['error_message'] = $this->lang->line('receivings_transaction_failed');
                    }

                    $this->load->view("receivings/receipt",$data);
                    $this->receiving_lib->clear_all();

                }

	}

	function labels($receiving_id)
	{
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		$this->receiving_lib->copy_entire_receiving($receiving_id);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=$this->lang->line('recvs_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payment_type']=$receiving_info['payment_type'];

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$supp_info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$supp_info->first_name.' '.$supp_info->last_name;
		}
		$data['receiving_id']='RECV '.$receiving_id;
		$this->load->view("receivings/labels",$data);
		$this->receiving_lib->clear_all();

	}

	function receipt($receiving_id)
	{
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		$this->receiving_lib->copy_entire_receiving($receiving_id);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=$this->lang->line('recvs_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payment_type']=$receiving_info['payment_type'];

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$supp_info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$supp_info->first_name.' '.$supp_info->last_name;
		}
		$data['receiving_id']='RECV '.$receiving_id;
		$this->load->view("receivings/receipt",$data);
		$this->receiving_lib->clear_all();

	}

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['cart']=$this->receiving_lib->get_cart();
		$data['modes']=array('receive'=>$this->lang->line('recvs_receiving'),'return'=>$this->lang->line('recvs_return'));
		$data['mode']=$this->receiving_lib->get_mode();
		$data['subtotal']=$this->receiving_lib->get_subtotal();
		$data['taxes']=$this->receiving_lib->get_taxes();
		$data['total']=$this->receiving_lib->get_total();
		$data['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		$data['payment_options']=array(
			$this->lang->line('sales_cash') => $this->lang->line('sales_cash'),
			$this->lang->line('sales_check') => $this->lang->line('sales_check'),
			$this->lang->line('sales_debit') => $this->lang->line('sales_debit'),
			$this->lang->line('sales_credit') => $this->lang->line('sales_credit')
		);

		$supplier_id=$this->receiving_lib->get_supplier();
		if($supplier_id!=-1)
		{
			$info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$info->first_name.' '.$info->last_name;
		}
		$this->load->view("receivings/receiving",$data);
	}

    function cancel_receiving()
    {
        //$this->load->view("receivings/receipt",$data);
    	$this->receiving_lib->clear_all();
    	$this->_reload();

    }
}
?>
