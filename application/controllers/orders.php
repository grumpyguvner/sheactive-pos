<?php
require_once ("secure_area.php");
require_once ("PSWebServiceLibrary.php");

class Orders extends Secure_area
{
	function __construct()
	{
		parent::__construct('orders');
		$this->load->library('order_lib');
	}

	function index()
	{
//		$this->_reload();
		$this->prestashop();
	}

        function prestashop()
        {
            $html = "<html><head><title>CRUD Tutorial - Customer's list</title></head><body>";
            define('DEBUG', true);											// Debug mode
            define('PS_SHOP_PATH', 'http://test.sheactive.co.uk/');		// Root path of your PrestaShop store
            define('PS_WS_AUTH_KEY', 'JWFI844NARM8VFPSIZP4FTBNDGBG5I2T');	// Auth key (Get it in your Back Office)
            // Here we make the WebService Call
            try
            {
                $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);

                // Here we set the option array for the Webservice : we want customers resources
                $opt['resource'] = 'customers';

                // Call
                $xml = $webService->get($opt);

                // Here we get the elements from children of customers markup "customer"
                $resources = $xml->customers->children();
            }
            catch (PrestaShopWebserviceException $e)
            {
                // Here we are dealing with errors
                $trace = $e->getTrace();
                if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
//                else echo 'Other error';
else print_r($e);
            }

            // We set the Title
            $html .= "<h1>Customer's List</h1>";

            $html .= "<table border='5'>";
            // if $resources is set we can lists element in it otherwise do nothing cause there's an error
            if (isset($resources))
            {
                $html .= "<tr><th>Id</th></tr>";
                foreach ($resources as $resource)
                {
                        // Iterates on the found IDs
                        $html .= "<tr><td>".$resource->attributes()."</td></tr>";
                }
            }
            $html .= "</table>";
            $html .= "</body></html>";
            die($html);
        }
	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function order_search()
	{
		$suggestions = $this->Order->get_order_search_suggestions($this->input->post('q'),$this->input->post('limit'));
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
		$this->sale_lib->set_customer($customer_id);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
		$this->sale_lib->set_mode($mode);
		$this->_reload();
	}

	//Alain Multiple Payments
//	function add_payment()
//	{
//		$data=array();
//		$this->form_validation->set_rules('amount_tendered', 'lang:orders_amount_tendered', 'numeric');
//
//		if ($this->form_validation->run() == FALSE)
//		{
//			$data['error']=$this->lang->line('orders_must_enter_numeric');
 //			$this->_reload($data);
 //			return;
//		}
//
//		$payment_type=$this->input->post('payment_type');
//		$payment_amount=$this->input->post('amount_tendered');
//		if(!$this->sale_lib->add_payment($payment_type,$payment_amount))
//		{
//			$data['error']='Unable to Add Payment! Please try again!';
//		}
//		$this->_reload($data);
//
//	}

	function add_payment()
	{
		$data['amount_due']=$this->sale_lib->get_amount_due();
		$this->load->view("orders/payment",$data);
        }

	function payment_save()
	{
		$data=array();
		$this->form_validation->set_rules('amount_tendered', 'lang:orders_amount_tendered', 'numeric');

		if ($this->form_validation->run() == FALSE)
		{
			$data['error']=$this->lang->line('orders_must_enter_numeric');
			$this->_reload($data);
			return;
		}

		$payment_due=$this->input->post('amount_due');
		$payment_type=$this->input->post('payment_type');
		$payment_amount=$this->input->post('amount_tendered');
		if(!$this->sale_lib->add_payment($payment_type,$payment_amount))
		{
			$data['error']='Unable to Add Payment! Please try again!';
		}
//		$this->_reload($data);
                $this->complete();

	}

	//Alain Multiple Payments
	function delete_payment($payment_id)
	{
		$this->sale_lib->delete_payment($payment_id);
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_receipt = $this->input->post("item");
		$quantity = $mode=="sale" ? 1:-1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_receipt) && $mode=='return')
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_receipt);
		}
		elseif(!$this->sale_lib->add_item($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('orders_unable_to_add_item');
		}
		
		if($this->sale_lib->out_of_stock($item_id_or_number_or_receipt) && $mode=="sale")
		{
			$data['warning'] = $this->lang->line('orders_quantity_less_than_zero');
		}
		$this->_reload($data);
	}

	function add_plu()
	{
		$data=array();
		$mode = $this->sale_lib->get_mode();
		$item_id_or_number_or_receipt = $this->input->post("plu");
		$quantity = $mode=="sale" ? 1:-1;

                //Temporary fix to lose the ?
                if (strlen($item_id_or_number_or_receipt)==14){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($item_id_or_number_or_receipt)==13){
                    $item_id_or_number_or_receipt = substr($item_id_or_number_or_receipt, 0, -1);
                }

		if($mode=='return' && $this->sale_lib->is_valid_receipt($item_id_or_number_or_receipt))
		{
			$this->sale_lib->return_entire_sale($item_id_or_number_or_receipt);
		}
		elseif(!$this->sale_lib->add_plu($item_id_or_number_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('orders_unable_to_add_item');
		}

		if($this->sale_lib->out_of_stock($item_id_or_number_or_receipt) && $mode=="sale")
		{
			$data['warning'] = $this->lang->line('orders_quantity_less_than_zero');
		}
		$this->_reload($data);
	}

	function edit_item($line)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:items_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|numeric');

                $description = $this->input->post("description");
                $ean_upc = $this->input->post("ean_upc");
                $serialnumber = $this->input->post("serialnumber");
		$price = $this->input->post("price");
		$quantity = $this->input->post("quantity");
		$discount = $this->input->post("discount");
		$discount_type = $this->input->post("discount_type");
		$discount_reason = $this->input->post("discount_reason");
                if ($discount!=0){
                    $this->form_validation->set_rules('discount_reason', 'lang:orders_discount_reason', 'required');
                }

		if ($this->form_validation->run() != FALSE)
		{
			$this->sale_lib->edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$discount,$discount_type,$discount_reason,$price);
		}
		else
		{
			$data['error']=$this->lang->line('orders_error_editing_item');
		}
		
		if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line)))
		{
			$data['warning'] = $this->lang->line('orders_quantity_less_than_zero');
		}


		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->sale_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_customer()
	{
		$this->sale_lib->delete_customer();
		$this->_reload();
	}

	function discount_item($item_number)
	{
		$items = $this->sale_lib->get_cart();
                $item = $items[$item_number];

		$data['line'] = $item_number;
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['price'] = $item['price'];
		$data['quantity'] = $item['quantity'];
		$data['discount'] = $item['discount'];
		$data['discount_type'] = $item['discount_type'];
		$data['discount_reason'] = $item['discount_reason'];
		$this->load->view("orders/discount",$data);
	}

	function discount_all()
	{
		$data['discount'] = 0;
		$data['discount_type'] = "%";
		$data['discount_reason'] = "";
		$this->load->view("orders/discount_all",$data);
	}

        function discount_all_apply()
        {
                $items = $this->sale_lib->get_cart();
                foreach ($items as $item)
                {
                    $line = $item['line'];
                    $description = $item['description'];
                    $ean_upc = $item['ean_upc'];
                    $serialnumber = $item['serialnumber'];
                    $price = $item['price'];
                    $quantity = $item['quantity'];
                    $discount = $this->input->post("discount");
                    $discount_type = $this->input->post("discount_type");
                    $discount_reason = $this->input->post("discount_reason");
                    $this->sale_lib->edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$discount,$discount_type,$discount_reason,$price);
                }
		$this->_reload();
        }

	function capture_ean($item_number)
	{
		$items = $this->sale_lib->get_cart();
                $item = $items[$item_number];

		$data['line'] = $item_number;
		$data['description'] = $item['description'];
		$data['ean_upc'] = $item['ean_upc'];
		$data['serialnumber'] = $item['serialnumber'];
		$data['price'] = $item['price'];
		$data['quantity'] = $item['quantity'];
		$data['discount'] = $item['discount'];
		$data['discount_type'] = $item['discount_type'];
		$data['discount_reason'] = $item['discount_reason'];
		$this->load->view("orders/capture_ean",$data);
	}

	function complete()
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('orders_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');
		//Alain Multiple payments
		$data['payments']=$this->sale_lib->get_payments();
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

                if($this->sale_lib->get_total()<>$this->sale_lib->get_payments_total()){
                    if($this->sale_lib->get_total()<0){
			$data['error'] = $this->lang->line('orders_must_enter_refund_type');
			$data['error'] .= $this->sale_lib->get_total()."<>".$this->sale_lib->get_payments_total();
			$this->_reload($data);
			return false;
                    }
                }
                if($this->Appconfig->get('orders_mode')!="TILL"){
                    $customer_req=false;
                    $comment_req=false;
                }else{
                    $customer_req=$this->sale_lib->customer_required();
                    $comment_req=$this->sale_lib->comment_required();
                }
                if($customer_req && $customer_id==-1)
                {
			$data['error'] = $this->lang->line('orders_must_enter_customer');
			$this->_reload($data);
			return false;
                }

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}

                if($comment_req && $comment=="")
                {
			$data['error'] = $this->lang->line('orders_must_enter_comment');
			$this->_reload($data);
			return false;
                }

		$total_payments = 0;
		$cash_given = 0;
		$cash_returned = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
                        if($payment['payment_type']=="CASH")
                        {
                            if($payment['payment_amount'] > 0){
                                $cash_given += $payment['payment_amount'];
                            }else{
                                $cash_returned += $payment['payment_amount'];
                            }
                        }
		}

		if (($this->sale_lib->get_mode() == 'sale') && ($total_payments <  to_currency_no_money($data['total'])))
		{
                        if($this->Appconfig->get('orders_mode')=="TILL"){
                            $data['error'] = $this->lang->line('orders_payment_not_cover_total');
                            $this->_reload($data);
                            return false;
                        }
       		}

		if ($this->sale_lib->get_mode() == 'sale')
                {
                    if ($total_payments > to_currency_no_money($data['total']))
                    {
                        if ($cash_given > 0)
                        {
                            $cash_returned = to_currency_no_money($data['total'])-$total_payments;
                            $difference = (($cash_returned*-1)-$cash_given);
                            if ($difference>0) { // Do not return more cash than given
                                $cash_returned = ($cash_given*-1);
                            }
                            $this->sale_lib->add_payment("CHANGE GIVEN: CASH",$cash_returned,false);
                            $data['payments']=$this->sale_lib->get_payments();
                        }
                    }
                    $total_payments=$this->sale_lib->get_payments_total(true);
                    if ($total_payments > to_currency_no_money($data['total']))
                    {
                            $other_returned = to_currency_no_money($data['total'])-$total_payments;
                            $this->sale_lib->add_payment("CHANGE GIVEN: OTHER",$other_returned,false);
                            $data['payments']=$this->sale_lib->get_payments();
                    }
                }

		//SAVE sale to database
		$prefix=$this->Appconfig->get('receipt_prefix');
		$data['sale_id']=$prefix.$this->Sale->save($data['cart'], $customer_id,$employee_id,$comment,$data['payments']);
		if ($data['sale_id'] == $prefix.'-1')
		{
			$data['error_message'] = $this->lang->line('orders_transaction_failed');
		}
		$this->sale_lib->clear_all();
                if($this->Appconfig->get('print_after_sale')=="RECEIPT"){
                    $this->load->view("orders/receipt",$data);
                    redirect('home/logout');
                }elseif($this->Appconfig->get('print_after_sale')=="REPORT"){
                    $this->load->view("orders/report",$data);
                    //redirect('orders');
                }
	}

	function receipt($sale_id)
	{
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart']=$this->sale_lib->get_cart();
		$data['payments']=$this->sale_lib->get_payments();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('orders_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payment_type']=$sale_info['payment_type'];
		$myPaid=$this->sale_lib->get_payments_total();
		$myTotal=$this->sale_lib->get_total();
		$data['amount_change']=($myPaid-$myTotal);
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
		//$data['sale_id']=$prefix.$sale_id;
		$data['sale_id']=$sale_id;

                $this->load->view("orders/receipt",$data);

		$this->sale_lib->clear_all();
                $this->_reload();

	}

	function _reload($data=array())
	{
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data['cart']=$this->sale_lib->get_cart();
		$data['modes']=array('sale'=>$this->lang->line('orders_sale'),'return'=>$this->lang->line('orders_return'));
		$data['mode']=$this->sale_lib->get_mode();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		//Alain Multiple Payments
		$data['payments_total']=$this->sale_lib->get_payments_total();
		$data['amount_due']=$this->sale_lib->get_amount_due();
		$data['payments']=$this->sale_lib->get_payments();
		$data['payment_options']=array(
			$this->lang->line('orders_cash') => $this->lang->line('orders_cash'),
			$this->lang->line('orders_check') => $this->lang->line('orders_check'),
			$this->lang->line('orders_debit') => $this->lang->line('orders_debit'),
			$this->lang->line('orders_credit') => $this->lang->line('orders_credit')
		);

                if($this->Appconfig->get('orders_mode')!="TILL"){
                    $data['customer_req']=false;
                }else{
                    $data['customer_req']=$this->sale_lib->customer_required();
                }
                
		$customer_id=$this->sale_lib->get_customer();
		if($customer_id!=-1)
		{
			$info=$this->Customer->get_info($customer_id);
			$data['customer']=$info->first_name.' '.$info->last_name;
		}
                $data['comment_req']=$this->sale_lib->comment_required();
                $data['comment']=$this->sale_lib->get_comment();
                if($this->Appconfig->get('orders_mode')=="TILL"){
                    $this->load->view("orders/register",$data);
                }else{
                    $this->load->view("orders/internet",$data);
                }
	}

    function cancel_sale()
    {
    	$this->sale_lib->clear_all();
    	$this->_reload();

    }

    function no_sale()
    {
    	$this->sale_lib->clear_all();

        $data['cart']=$this->sale_lib->get_cart();
        $data['subtotal']=$this->sale_lib->get_subtotal();
        $data['taxes']=$this->sale_lib->get_taxes();
        $data['total']=$this->sale_lib->get_total();
        $data['receipt_title']=$this->lang->line('orders_receipt');
        $data['transaction_time']= date('m/d/Y h:i:s a');
        $customer_id=$this->sale_lib->get_customer();
        $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
        $comment = $this->input->post('comment');
        $emp_info=$this->Employee->get_info($employee_id);
        $payment_type = $this->input->post('payment_type');
        $data['payment_type']=$this->input->post('payment_type');
        //Alain Multiple payments
        $data['payments']=$this->sale_lib->get_payments();
        $data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
        $data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

        if($customer_id!=-1)
        {
                $cust_info=$this->Customer->get_info($customer_id);
                $data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
        }

        $total_payments = 0;

        foreach($data['payments'] as $payment)
        {
                $total_payments += $payment['payment_amount'];
        }

        if (($this->sale_lib->get_mode() == 'sale') && ($total_payments <  to_currency_no_money($data['total'])))
        {
                $data['error'] = $this->lang->line('orders_payment_not_cover_total');
                $this->_reload($data);
                return false;
        }

        //SAVE sale to database
	$prefix=$this->Appconfig->get('receipt_prefix');
        $data['sale_id']=$prefix.$this->Sale->no_sale($employee_id,$comment);
        if ($data['sale_id'] == $prefix.'-1')
        {
                $data['error_message'] = $this->lang->line('orders_transaction_failed');
        }
        $this->load->view("orders/no_sale",$data);
        $this->sale_lib->clear_all();

        redirect('home/logout');

    }

}
?>
