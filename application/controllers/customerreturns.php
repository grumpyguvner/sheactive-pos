<?php
require_once ("secure_area.php");
class Customerreturns extends Secure_area
{
	function __construct()
	{
		parent::__construct('customerreturns');
		$this->load->library('customerreturn_lib');
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

	function customer_search()
	{
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_customer()
	{
		$customer_id = $this->input->post("customer");
		$this->customerreturn_lib->set_customer($customer_id);
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

		if($this->customerreturn_lib->is_valid_receipt($item_id_or_number_or_receipt))
		{
			$this->customerreturn_lib->return_entire_customerreturn($item_id_or_number_or_receipt);
		}
		elseif(!$this->customerreturn_lib->add_item($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('customerreturns_unable_to_add_item');
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

		if($this->customerreturn_lib->is_valid_receipt($item_id_or_number_or_receipt))
		{
			$this->customerreturn_lib->return_entire_customerreturn($item_id_or_number_or_receipt);
		}
		elseif(!$this->customerreturn_lib->add_plu($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('customerreturns_unable_to_add_item');
		}

		$this->_reload($data);
	}

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('cost_price', 'lang:items_costprice', 'required|numeric');
		$this->form_validation->set_rules('price', 'lang:items_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|numeric');

        $description = $this->input->post("description");
        $ean_upc = $this->input->post("ean_upc");
        $serialnumber = $this->input->post("serialnumber");
		$price = $this->input->post("price");
		$cost_price = $this->input->post("cost_price");
		$quantity = $this->input->post("quantity");
		$reason_code = $this->input->post("reason_code");
		$restock = $this->input->post("restock");
		$faulty = $this->input->post("faulty");
		$comment = $this->input->post("comment");
        if ($reason_code==0)
        {
            $this->form_validation->set_rules('comment', 'lang:customerreturns_reason_code', 'required');
        }
		if ($restock!=1)
        {
            $this->form_validation->set_rules('comment', 'lang:customerreturns_comment', 'required');
        }
        
		if ($this->form_validation->run() != FALSE)
		{
			$this->customerreturn_lib->edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$reason_code,$restock,$faulty,$comment,$price,$cost_price);
		}
		else
		{
			$data['error']=$this->lang->line('customerreturns_error_editing_item');
		}
		
		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->customerreturn_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_customer()
	{
		$this->customerreturn_lib->delete_customer();
		$this->_reload();
	}

	function reason_code_item($item_number)
	{
		$items = $this->customerreturn_lib->get_cart();
                $item = $items[$item_number];

		$data['line'] = $item_number;
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['price'] = $item['price'];
		$data['cost_price'] = $item['cost_price'];
		$data['quantity'] = $item['quantity'];
		$data['reason_code'] = $item['reason_code'];
		$data['restock'] = $item['restock'];
		$data['faulty'] = $item['faulty'];
		$data['comment'] = $item['comment'];
		$this->load->view("customerreturns/reason_code",$data);
	}

	function capture_ean($item_number)
	{
		$items = $this->customerreturn_lib->get_cart();
                $item = $items[$item_number];

		$data['line'] = $item_number;
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['price'] = $item['price'];
		$data['cost_price'] = $item['cost_price'];
		$data['quantity'] = $item['quantity'];
		$data['reason_code'] = $item['reason_code'];
		$data['restock'] = $item['restock'];
		$data['faulty'] = $item['faulty'];
		$data['comment'] = $item['comment'];
		$this->load->view("customerreturns/capture_ean",$data);
	}
	
	function restock($item_number)
	{
		$items = $this->customerreturn_lib->get_cart();
                $item = $items[$item_number];

		$data['line'] = $item_number;
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['price'] = $item['price'];
		$data['cost_price'] = $item['cost_price'];
		$data['quantity'] = $item['quantity'];
		$data['reason_code'] = $item['reason_code'];
		$data['restock'] = $item['restock'];
		$data['faulty'] = $item['faulty'];
		$data['comment'] = $item['comment'];
		$this->load->view("customerreturns/restock",$data);
	}
	
	function complete()
	{
		$data['cart']=$this->customerreturn_lib->get_cart();
		$data['subtotal']=$this->customerreturn_lib->get_subtotal();
		$data['total']=$this->customerreturn_lib->get_total();
		$data['receipt_title']=$this->lang->line('customerreturns_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->customerreturn_lib->get_customer();
		$orderref=$this->input->post('orderref');
		$comment = $this->input->post('comment');
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

        $customer_req=$this->customerreturn_lib->customer_required();
        $orderref_req=$this->customerreturn_lib->orderref_required();
        $comment_req=$this->customerreturn_lib->comment_required();
        if($customer_req && $customer_id==-1)
        {
			$data['error'] = $this->lang->line('customerreturns_must_enter_customer');
			$this->_reload($data);
			return false;
        }

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}

		foreach ($data['cart'] as $line=>$item)
		{
			if($item['reason_code']==0 && $item['faulty']==0)
			{
				$data['error'] = $this->lang->line('customerreturns_must_enter_reason');
				$this->_reload($data);
				return false;
			}
		}
		
        if($orderref_req && $orderref=="")
        {
			$data['error'] = $this->lang->line('customerreturns_must_enter_orderref');
			$this->_reload($data);
			return false;
        }

        if($comment_req && $comment=="")
        {
			$data['error'] = $this->lang->line('customerreturns_must_enter_comment');
			$this->_reload($data);
			return false;
        }
        
		//SAVE customerreturn to database
		$prefix="RTN ";
		$data['customerreturn_id']=$prefix.$this->Customerreturn->save($data['cart'],$orderref,$customer_id,$employee_id,$comment);
		if ($data['customerreturn_id'] == $prefix.'-1')
		{
			$data['error_message'] = $this->lang->line('customerreturns_transaction_failed');
		}
		$this->customerreturn_lib->clear_all();
        	$this->load->view("customerreturns/report",$data);
	}

	function receipt($customerreturn_id)
	{
		$customerreturn_info = $this->Customerreturn->get_info($customerreturn_id)->row_array();
		$this->customerreturn_lib->copy_entire_customerreturn($customerreturn_id);
		$data['cart']=$this->customerreturn_lib->get_cart();
		$data['subtotal']=$this->customerreturn_lib->get_subtotal();
		$data['total']=$this->customerreturn_lib->get_total();
		$data['receipt_title']=$this->lang->line('customerreturns_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($customerreturn_info['customerreturn_time']));
		$customer_id=$this->customerreturn_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
//		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;
		$data['employee']=$emp_info->first_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
			$data['customer_address_1']=$cust_info->address_1;
			$data['customer_address_2']=$cust_info->address_2;
			$data['customer_telephone']=$cust_info->phone_number;
		}
		$prefix=$this->Appconfig->get('receipt_prefix');
		//$data['customerreturn_id']=$prefix.$customerreturn_id;
		$data['customerreturn_id']=$customerreturn_id;

                $this->load->view("customerreturns/receipt",$data);

		$this->customerreturn_lib->clear_all();
                $this->_reload();

	}

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['cart']=$this->customerreturn_lib->get_cart();
		$data['subtotal']=$this->customerreturn_lib->get_subtotal();
		$data['total']=$this->customerreturn_lib->get_total();
		$data['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);

        $data['customer_req']=$this->customerreturn_lib->customer_required();
		$customer_id=$this->customerreturn_lib->get_customer();
		if($customer_id!=-1)
		{
			$info=$this->Customer->get_info($customer_id);
			$data['customer']=$info->first_name.' '.$info->last_name;
		}

		$data['orderref_req']=$this->customerreturn_lib->orderref_required();
		$data['orderref']=$this->customerreturn_lib->get_orderref();
		$data['comment_req']=$this->customerreturn_lib->comment_required();
		$data['comment']=$this->customerreturn_lib->get_comment();
		
        $this->load->view("customerreturns/internet",$data);
	}

    function cancel_customerreturn()
    {
    	$this->customerreturn_lib->clear_all();
    	$this->_reload();

    }

}
?>
