<?php
class Customerreturn_lib
{
	var $CI;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function orderref_required()
	{
            //not mandatory until we switch away from Netro
            return false;
	}
	
	function customer_required()
	{
            //not mandatory until we switch away from Netro
            return false;
	}
	
	function comment_required()
	{
            //if we have returned any items then
            //a comment is required
//            $items = $this->get_cart();
//            foreach ($items as $item)
//            {
//                    if($item['quantity'] > 0)
//                        return true;
//            }

            return false;
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('returnCart'))
			$this->set_cart(array());

		return $this->CI->session->userdata('returnCart');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('returnCart',$cart_data);
	}

	function get_comment()
	{
		if(!$this->CI->session->userdata('returnComment'))
			$this->set_comment("");

		return $this->CI->session->userdata('returnComment');
	}

	function set_comment($comment)
	{
		$this->CI->session->set_userdata('returnComment',$comment);
	}

	function get_customer()
	{
		if(!$this->CI->session->userdata('returnCustomer'))
			$this->set_customer(-1);

		return $this->CI->session->userdata('returnCustomer');
	}

	function set_customer($customer_id)
	{
		$this->CI->session->set_userdata('returnCustomer',$customer_id);
	}
	
	function get_orderref()
	{
		if(!$this->CI->session->userdata('returnOrderref'))
			$this->set_orderref("");

		return $this->CI->session->userdata('returnOrderref');
	}

	function set_orderref($orderref)
	{
		$this->CI->session->set_userdata('returnOrderref',$orderref);
	}
	
	function add_plu($plu,$quantity=1)
	{
log_message('debug', "PLU:".$plu);
            $item_id = $this->CI->Item->get_id_by_plu($plu);
log_message('debug', "ITEM:".$item_id);
            return $this->add_item($item_id,$quantity);
        }

	function add_item($item_id,$quantity=1)
	{
		//make sure item exists
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
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
		
		//This will need to be retrieved from the order if we have an orderref
		$price=$this->CI->Item->get_info($item_id)->unit_price;


		$item = array(($insertkey)=>
		array(
			'item_id'=>$item_id,
			'line'=>$insertkey,
			'name'=>$this->CI->Item->get_info($item_id)->name,
			'item_number'=>$this->CI->Item->get_info($item_id)->item_number,
			'ean_upc'=>$this->CI->Item->get_info($item_id)->ean_upc,
			'description'=>$this->CI->Item->get_info($item_id)->description,
			'serialnumber'=>'',
			'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
			'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
			'location_id'=>$this->CI->Item->get_info($item_id)->location_id,
			'location'=>$this->CI->Item->get_info($item_id)->location_id!=null ? $this->CI->Location->get_info($this->CI->Item->get_info($item_id)->location_id)->location_ref:'NOWHERE',
			'quantity'=>$quantity,
			'reason_code'=>0,
            'restock'=>1,
            'faulty'=>0,
			'comment'=>"",
			'cost_price'=>$this->CI->Item->get_info($item_id)->cost_price,
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
                'reason_code'=>$item['reason_code'],
                'restock'=>$item['restock'],
                'faulty'=>$item['faulty'],
				'comment'=>$item['comment'],
	            'location_id'=>$item['location_id'],
	            'location'=>$item['location'],
	            'cost_price'=>$item['cost_price'],
				'price'=>$item['price']
				)
			);
			$newitems+=$newitem;
		}

		$this->set_cart($newitems);
		
		return true;

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

	function edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$reason_code,$restock,$faulty,$comment,$price,$cost_price)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$items[$line]['description'] = $description;
			$items[$line]['ean_upc'] = $ean_upc;
			$items[$line]['serialnumber'] = $serialnumber;
			$items[$line]['quantity'] = $quantity;
			$items[$line]['reason_code'] = $reason_code;
			$items[$line]['restock'] = $restock;
			$items[$line]['faulty'] = $faulty;
			$items[$line]['comment'] = $comment;
			$items[$line]['cost_price'] = $cost_price;
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

	function is_valid_receipt($receipt_customerreturn_id)
	{
		//RTN #
		$pieces = explode(' ',$receipt_customerreturn_id);

		if(count($pieces)==2 && $pieces[0]=="RTN")
		{
			return $this->CI->Customerreturn->exists($pieces[1]);
		}

		return false;
	}

	function return_entire_customerreturn($receipt_customerreturn_id)
	{
		//RTN #
		$pieces = explode(' ',$receipt_customerreturn_id);
		$customerreturn_id = $pieces[1];

		$this->empty_cart();
		$this->delete_customer();
		$this->delete_orderref();
		$this->delete_comment();
		
		foreach($this->CI->Customerreturn->get_customerreturn_items($customerreturn_id)->result() as $row)
		{
			$this->add_item($row->item_id,-$row->quantity_returned,$row->reason_code,$row->restock,$row->faulty,$row->comment,$row->item_unit_price,null,$row->description,$row->serialnumber);
		}
		$this->set_customer($this->CI->Customerreturn->get_customer($customerreturn_id)->person_id);
		$this->set_orderref($this->CI->Customerreturn->get_info($customerreturn_id)->orderref);
		$this->set_comment($this->CI->Customerreturn->get_info($customerreturn_id)->comment);
	}

	function copy_entire_customerreturn($customerreturn_id)
	{
		$this->empty_cart();
		$this->delete_customer();
		$this->delete_orderref();
		$this->delete_comment();
		
		foreach($this->CI->Customerreturn->get_customerreturn_items($customerreturn_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_returned,$row->reason_code,$row->restock,$row->faulty,$row->comment,$row->item_unit_price,null,$row->description,$row->serialnumber);
		}
		$this->set_customer($this->CI->Customerreturn->get_customer($customerreturn_id)->person_id);
		$this->set_orderref($this->CI->Customerreturn->get_info($customerreturn_id)->orderref);
		$this->set_comment($this->CI->Customerreturn->get_info($customerreturn_id)->comment);
		
	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('returnCart');
	}

	function delete_customer()
	{
		$this->CI->session->unset_userdata('returnCustomer');
	}
	
	function delete_orderref()
	{
		$this->CI->session->unset_userdata('returnOrderref');
	}
	
	function delete_comment()
	{
		$this->CI->session->unset_userdata('returnComment');
	}
	
	function clear_all()
	{
		$this->empty_cart();
		$this->delete_customer();
		$this->delete_orderref();
		$this->delete_comment();
	}

	function get_subtotal()
	{
		$subtotal = 0;
		foreach($this->get_cart() as $item)
		{
		    $subtotal+=(($item['cost_price'])*$item['quantity']);
		}
		return to_currency_no_money($subtotal);
	}

	function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
        	$total+=(($item['price'])*$item['quantity']);
		}

		return to_currency_no_money($total);
	}
}
?>
