<?php
class Sale_lib
{
	var $CI;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function customer_required()
	{
            //if we have returned any items then
            //customer is required
            $items = $this->get_cart();
            foreach ($items as $item)
            {
                    if($item['quantity'] < 0)
                        return true;
            }

            return false;
	}

	function comment_required()
	{
            //if we have returned any items then
            //a comment is required
            $items = $this->get_cart();
            foreach ($items as $item)
            {
                    if($item['quantity'] < 0)
                        return true;
            }

            return false;
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('cart'))
			$this->set_cart(array());

		return $this->CI->session->userdata('cart');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cart',$cart_data);
	}

	//Alain Multiple Payments
	function get_payments()
	{
		if(!$this->CI->session->userdata('payments'))
			$this->set_payments(array());

		return $this->CI->session->userdata('payments');
	}

	//Alain Multiple Payments
	function set_payments($payments_data)
	{
		$this->CI->session->set_userdata('payments',$payments_data);
	}

	//Alain Multiple Payments
	function add_payment($payment_id,$payment_amount,$accumulate=true)
	{
		$payments=$this->get_payments();
		$payment = array($payment_id=>
		array(
			'payment_type'=>$payment_id,
			'payment_amount'=>$payment_amount
			)
		);

		//payment_method already exists, add to payment_amount
		if(isset($payments[$payment_id]) && $accumulate)
		{
			$payments[$payment_id]['payment_amount']+=$payment_amount;
		}
		else
		{
			//add to existing array
			$payments+=$payment;
		}

		$this->set_payments($payments);
		return true;

	}

	//Alain Multiple Payments
	function edit_payment($payment_id,$payment_amount)
	{
		$payments = $this->get_payments();
		if(isset($payments[$payment_id]))
		{
			$payments[$payment_id]['payment_type'] = $payment_id;
			$payments[$payment_id]['payment_amount'] = $payment_amount;
			$this->set_payments($payment_id);
		}

		return false;
	}

	//Alain Multiple Payments
	function delete_payment($payment_id)
	{
		$payments=$this->get_payments();
		unset($payments[$payment_id]);
		$this->set_payments($payments);
	}

	//Alain Multiple Payments
	function empty_payments()
	{
		$this->CI->session->unset_userdata('payments');
	}

	//Alain Multiple Payments
	function get_payments_total($inc_change = false)
	{
		$subtotal = 0;
		foreach($this->get_payments() as $payments)
		{
                    if(substr($payments['payment_type'],0,13)!="CHANGE GIVEN:" || $inc_change)
                        $subtotal+=$payments['payment_amount'];
		}
		return to_currency_no_money($subtotal);
	}

	//Alain Multiple Payments
	function get_amount_due()
	{
		$amount_due=0;
		$payment_total = $this->get_payments_total();
		$sales_total=$this->get_total();
		$amount_due=to_currency_no_money($sales_total - $payment_total);
		return $amount_due;
	}

	function get_comment()
	{
		if(!$this->CI->session->userdata('comment'))
			$this->set_comment("");

		return $this->CI->session->userdata('comment');
	}

	function set_comment($comment)
	{
		$this->CI->session->set_userdata('comment',$comment);
	}

	function get_customer()
	{
		if(!$this->CI->session->userdata('customer'))
			$this->set_customer(-1);

		return $this->CI->session->userdata('customer');
	}

	function set_customer($customer_id)
	{
		$this->CI->session->set_userdata('customer',$customer_id);
	}

	function get_mode()
	{
		if(!$this->CI->session->userdata('sale_mode'))
			$this->set_mode('sale');

		return $this->CI->session->userdata('sale_mode');
	}

	function set_mode($mode)
	{
		$this->CI->session->set_userdata('sale_mode',$mode);
	}

	function add_plu($plu,$quantity=1,$discount=0,$discount_type='%',$discount_reason='',$price=null,$tax=null,$description=null,$serialnumber=null)
	{
            $item_id = $this->CI->Item->get_id_by_plu($plu);
//            return $this->add_item($item_id,$quantity,$discount,$discount_type,$discount_reason,$price,$tax,$description,$serialnumber);
            return $this->add_item($item_id,$quantity);
        }

	function add_item($item_id,$quantity=1,$discount=0,$discount_type='%',$discount_reason='',$price=null,$tax=null,$description=null,$serialnumber=null)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

                //2011-11-22 MH Amended to stop sales of items showing no stock.
                if ($quantity > 0){
                    $item = $this->CI->Item->get_info($item_id);
                    $quanity_added = $this->get_quantity_already_added($item_id);
                    if ($item->quantity - ($quanity_added+1) < 0)
                    {
                            return false;
                    }
                }

		//Always add the item at the begining of the list so that it appears
		//at the top of the screen

		//Get all items in the cart so far...
		$items = $this->get_cart();
		//Initialise the new array
		$newitems = array();


		$insertkey=0;
		//Add the new item
		//array/cart records are identified by $insertkey and item_id is just another field.
                $discount=round($discount,2);
		if($price==null){
                    $price=round($this->CI->Item->get_info($item_id)->unit_price,2);
		}
		$retail_price=round($this->CI->Item->get_info($item_id)->retail_price,2);
                if($retail_price==null){
                    $retail_price=round($this->CI->Item->get_info($item_id)->unit_price,2);
                }
		if($discount==0){
			$discount=$retail_price - $price;
			$discount_type="£";
			$price=$retail_price;
		}

		$supplier_id=$this->CI->Item->get_info($item_id)->supplier_id;
		$supplier_discount=$this->CI->Supplier->get_info($supplier_id)->discount_percent;
		$amount=round($retail_price*($supplier_discount/100),2);
		if($amount > $discount){
			$discount=$supplier_discount;
			$discount_type="%";
		}

		$item = array(($insertkey)=>
		array(
			'item_id'=>$item_id,
			'line'=>$insertkey,
			'name'=>$this->CI->Item->get_info($item_id)->name,
			'item_number'=>$this->CI->Item->get_info($item_id)->item_number,
			'ean_upc'=>$this->CI->Item->get_info($item_id)->ean_upc,
			'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
			'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
			'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
			'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
                        'location_id'=>$this->CI->Item->get_info($item_id)->location_id,
                        'location'=>$this->CI->Item->get_info($item_id)->location_id!=null ? $this->CI->Location->get_info($this->CI->Item->get_info($item_id)->location_id)->location_ref:'NOWHERE',
			'quantity'=>$quantity,
                        'discount'=>$discount,
                        'discount_type'=>$discount_type,
                        'discount_reason'=>$discount_reason,
			'price'=>$price
			)
		);
		$newitems+=$item;

	        //We need to loop through all items in the cart.
	        //and add them on to the new array

		foreach ($items as $item)
		{
			$insertkey=$insertkey+1;
			$newitem = array(($insertkey)=>
			array(
				'item_id'=>$item['item_id'],
				'line'=>$insertkey,
				'name'=>$item['name'],
				'item_number'=>$item['item_number'],
				'ean_upc'=>$item['ean_upc'],
				'description'=>$item['description'],
				'serialnumber'=>$item['serialnumber'],
				'allow_alt_description'=>$item['allow_alt_description'],
				'is_serialized'=>$item['is_serialized'],
				'quantity'=>$item['quantity'],
                	        'discount'=>$item['discount'],
                        	'discount_type'=>$item['discount_type'],
	                        'discount_reason'=>$item['discount_reason'],
	                        'location_id'=>$item['location_id'],
	                        'location'=>$item['location'],
				'price'=>$item['price']
				)
			);
			$newitems+=$newitem;
		}

		$this->set_cart($newitems);
		return true;

	}
	
	function out_of_stock($item_id)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}
		
		$item = $this->CI->Item->get_info($item_id);
		$quanity_added = $this->get_quantity_already_added($item_id);
		
		if ($item->quantity - $quanity_added < 0)
		{
			return true;
		}
		
		return false;
	}
	
	function get_quantity_already_added($item_id)
	{
		$items = $this->get_cart();
		$quanity_already_added = 0;
		foreach ($items as $item)
		{
			if($item['item_id']==$item_id)
			{
				$quanity_already_added+=$item['quantity'];
			}
		}
		
		return $quanity_already_added;
	}
	
	function get_item_id($line_to_get)
	{
		$items = $this->get_cart();

		foreach ($items as $line=>$item)
		{
			if($line==$line_to_get)
			{
				return $item['item_id'];
			}
		}
		
		return -1;
	}

	function edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$discount,$discount_type,$discount_reason,$price)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$items[$line]['description'] = $description;
			$items[$line]['ean_upc'] = $ean_upc;
			$items[$line]['serialnumber'] = $serialnumber;
			$items[$line]['quantity'] = $quantity;
			$items[$line]['discount'] = $discount;
			$items[$line]['discount_type'] = $discount_type;
			$items[$line]['discount_reason'] = $discount_reason;
			$items[$line]['price'] = $price;
			$this->set_cart($items);
		}
		//update ean_upc if necessary
                $cur_item_id = $items[$line]['item_id'];
                if(!$items[$line]['ean_upc']==$this->CI->Item->get_info($cur_item_id)->ean_upc)
                {
                    $item_data = array(
                    'ean_upc'=>$items[$line]['ean_upc']
                    );
                    $employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;
                    $this->CI->Item->save($item_data,$cur_item_id);
                }

		return false;
	}

	function is_valid_receipt($receipt_sale_id)
	{
		$receipt_prefix = $this->CI->Appconfig->get('receipt_prefix');

		$pieces[] = substr($receipt_sale_id,0,strlen($receipt_prefix));
		$pieces[] = substr($receipt_sale_id,strlen($receipt_prefix));

		if($pieces[0]==$receipt_prefix)
		{
			return $this->CI->Sale->exists($pieces[1]);
		}

		return false;
	}

	function return_entire_sale($receipt_sale_id)
	{
		$receipt_prefix = $this->CI->Appconfig->get('receipt_prefix');

		$pieces[] = substr($receipt_sale_id,0,strlen($receipt_prefix));
		$pieces[] = substr($receipt_sale_id,strlen($receipt_prefix));

		$sale_id = $pieces[1];

		$this->empty_cart();
		$this->delete_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id,-$row->quantity_purchased,$row->discount,$row->discount_type,$row->discount_reason,$row->item_unit_price,null,$row->description,$row->serialnumber);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);
	}

	function copy_entire_sale($sale_id)
	{
		$this->empty_cart();
		$this->delete_customer();

		foreach($this->CI->Sale->get_sale_items($sale_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_purchased,$row->discount,$row->discount_type,$row->discount_reason,$row->item_unit_price,null,$row->description,$row->serialnumber);
		}
		foreach($this->CI->Sale->get_sale_payments($sale_id)->result() as $row)
		{
			$this->add_payment($row->payment_type,$row->payment_amount);
		}
		$this->set_customer($this->CI->Sale->get_customer($sale_id)->person_id);

	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cart');
	}

	function delete_customer()
	{
		$this->CI->session->unset_userdata('customer');
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('sale_mode');
	}

	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		//Alain Multiple Payments
		$this->empty_payments();
		$this->delete_customer();
	}

	function get_taxes()
	{
		$customer_id = $this->get_customer();
		$customer = $this->CI->Customer->get_info($customer_id);

                return array();

		//Do not charge sales tax if we have a customer that is not taxable
		if (!$customer->taxable and $customer_id!=-1)
		{
		   return array();
		}

		$taxes = array();
		foreach($this->get_cart() as $line=>$item)
		{
			$tax_info = $this->CI->Item_taxes->get_info($item['item_id']);

			foreach($tax_info as $tax)
			{
				$name = $tax['percent'].'% ' . $tax['name'];
                                switch ($item['discount_type']){
                                    case '%':
                                        $myDisc = $item['price'] * $item['discount'] / 100;
                                        break;
                                    case '£':
                                    case '€':
                                        $myDisc = $item['discount'];
                                        break;
                                    default:
                                        $myDisc = 0;
                                }
				$tax_amount=(($item['price']-$myDisc)*$item['quantity'])*(($tax['percent'])/100);


				if (!isset($taxes[$name]))
				{
					$taxes[$name] = 0;
				}
				$taxes[$name] += $tax_amount;
			}
		}

		return $taxes;
	}

	function get_subtotal()
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = $item['price'] * $item['discount'] / 100;
                            break;
                        case '£':
                        case '€':
                            $myDisc = $item['discount'];
                            break;
                        default:
                            $myDisc = 0;
                    }
		    $subtotal+=(($item['price']-$myDisc)*$item['quantity']);
		}
		return to_currency_no_money($subtotal);
	}

	function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = $item['price'] * $item['discount'] / 100;
                            break;
                        case '£':
                        case '€':
                            $myDisc = $item['discount'];
                            break;
                        default:
                            $myDisc = 0;
                    }
                    $total+=(($item['price']-$myDisc)*$item['quantity']);
		}

		foreach($this->get_taxes() as $tax)
		{
			$total+=$tax;
		}

		return to_currency_no_money($total);
	}
}
?>
