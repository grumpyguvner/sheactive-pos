<?php
class Receiving_lib
{
	var $CI;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('cartRecv'))
			$this->set_cart(array());

		return $this->CI->session->userdata('cartRecv');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cartRecv',$cart_data);
	}

	function get_supplier()
	{
		if(!$this->CI->session->userdata('supplier'))
			$this->set_supplier(-1);

		return $this->CI->session->userdata('supplier');
	}

	function set_supplier($supplier_id)
	{
		$this->CI->session->set_userdata('supplier',$supplier_id);
	}

	function get_mode()
	{
		if(!$this->CI->session->userdata('recv_mode'))
			$this->set_mode('receive');

		return $this->CI->session->userdata('recv_mode');
	}

	function set_mode($mode)
	{
		$this->CI->session->set_userdata('recv_mode',$mode);
	}

	function add_plu($plu,$quantity=1,$discount=0,$discount_type='%',$discount_reason='',$cost_price=null,$tax=null,$description=null,$serialnumber=null,$location_id=null)
	{
log_message('debug', "PLU:".$plu);
            $item_id = $this->CI->Item->get_id_by_plu($plu);
log_message('debug', "ITEM:".$item_id);
//            return $this->add_item($item_id,$quantity,$discount,$discount_type,$discount_reason,$cost_price,$tax,$description,$serialnumber,$location_id);
            return $this->add_item($item_id,$quantity);
        }

	function add_item($item_id,$quantity=1,$discount=0,$discount_type='%',$discount_reason='',$cost_price=null,$tax=null,$description=null,$serialnumber=null,$location_id=null)
	{
		//make sure item exists in database.
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		//Get items in the receiving so far.
		$items = $this->get_cart();

                //We need to loop through all items in the cart.
                //If the item is already there, get it's key($updatekey).

                $itemalreadyinsale=FALSE;        //We did not find the item yet.
		$updatekey=0;                    //Key to use to update(quantity)

		foreach ($items as $item)
		{
			if($item['item_id']==$item_id)
			{
				$itemalreadyinsale=TRUE;
				$updatekey=$item['line'];
			}
		}

		//Item already exists
		if($itemalreadyinsale)
		{
			$items[$updatekey]['quantity']+=$quantity;
		}
		else
		{
                    //add the new item to the begining of the array so that
                    //it appears at the top of the screen
                    //initialise the new array:
                    $newitems = array();
                    $insertkey=0;                    //Key to use for new entry.

                    //array records are identified by $insertkey and item_id is just another field.
                    $newitem = array(($insertkey)=>
                    array(
                            'item_id'=>$item_id,
                            'line'=>$insertkey,
                            'name'=>$this->CI->Item->get_info($item_id)->name,
                            'item_number'=>$this->CI->Item->get_info($item_id)->item_number,
                            'retail_price'=>$this->CI->Item->get_info($item_id)->retail_price,
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
                            'cost_price'=>$cost_price!=null ? $cost_price: $this->CI->Item->get_info($item_id)->cost_price
                            )
                    );
                    //add to existing array
                    $newitems+=$newitem;

                    //now loop through the existing items and add them to the array:
                    foreach ($items as $item)
                    {
                        $insertkey=$insertkey+1;                    //Key to use for new entry.

                        //array records are identified by $insertkey and item_id is just another field.
                        $newitem = array(($insertkey)=>
                        array(
                                'item_id'=>$item['item_id'],
                                'line'=>$insertkey,
                                'name'=>$item['name'],
                                'item_number'=>$item['item_number'],
                            	'retail_price'=>$item['retail_price'],
                                'ean_upc'=>$item['ean_upc'],
                                'description'=>$item['description'],
                                'serialnumber'=>$item['serialnumber'],
                                'allow_alt_description'=>$item['allow_alt_description'],
                                'is_serialized'=>$item['is_serialized'],
	                        'location_id'=>$item['location_id'],
	                        'location'=>$item['location'],
                                'quantity'=>$item['quantity'],
                                'discount'=>$item['discount'],
                                'discount_type'=>$item['discount_type'],
                                'discount_reason'=>$item['discount_reason'],
                                'cost_price'=>$item['cost_price']
                                )
                        );
                        //add to existing array
                        $newitems+=$newitem;
                    }

                    //overide old array with new array
                    $items = $newitems;
		}

		$this->set_cart($items);
		return true;
	}

	function edit_item($line,$description,$ean_upc,$serialnumber,$quantity,$discount,$discount_type,$discount_reason,$cost_price,$location_id)
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
			$items[$line]['cost_price'] = $cost_price;
			$items[$line]['location_id'] = $location_id;
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
		//update location if necessary
                $cur_item_id = $items[$line]['item_id'];
                if(!$items[$line]['location_id']==$this->CI->Item->get_info($cur_item_id)->location_id)
                {
                    $item_data = array(
                    'location_id'=>$items[$line]['location_id']
                    );
                    $employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;
                    $this->CI->Item->save($item_data,$cur_item_id);
                }

		return false;
	}

	function is_valid_receipt($receipt_receiving_id)
	{
		//RECV #
		$pieces = explode(' ',$receipt_receiving_id);

		if(count($pieces)==2 && $pieces[0]=="RECV")
		{
			return $this->CI->Receiving->exists($pieces[1]);
		}

		return false;
	}

	function return_entire_receiving($receipt_receiving_id)
	{
		//RECV #
		$pieces = explode(' ',$receipt_receiving_id);
		$receiving_id = $pieces[1];

		$this->empty_cart();
		$this->delete_supplier();

		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$this->add_item($row->item_id,-$row->quantity_purchased,$row->discount,$row->discount_type,$row->discount_reason,$row->item_cost_price,null,$row->description,$row->serialnumber,$row->location_id);
		}
		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);
	}

	function copy_entire_receiving($receiving_id)
	{
		$this->empty_cart();
		$this->delete_supplier();

		foreach($this->CI->Receiving->get_receiving_items($receiving_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_purchased,$row->discount,$row->discount_type,$row->discount_reason,$row->item_cost_price,null,$row->description,$row->serialnumber,$row->location_id);
		}
		$this->set_supplier($this->CI->Receiving->get_supplier($receiving_id)->person_id);

	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cartRecv');
	}

	function delete_supplier()
	{
		$this->CI->session->unset_userdata('supplier');
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('receiving_mode');
	}

	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->delete_supplier();
	}

	function get_taxes()
	{
		$taxes = array();
		foreach($this->get_cart() as $line=>$item)
		{
			$tax_info = $this->CI->Item_taxes->get_info($item['item_id']);

			foreach($tax_info as $tax)
			{
				$name = $tax['percent'].'% ' . $tax['name'];
                                switch ($item['discount_type']){
                                    case '%':
                                        $myDisc = $item['cost_price'] * $item['discount'] / 100;
                                        break;
                                    case '£':
                                    case '¬':
                                        $myDisc = $item['discount'];
                                        break;
                                    default:
                                        $myDisc = 0;
                                }
				$tax_amount=(($item['cost_price']-$myDisc)*$item['quantity'])*(($tax['percent'])/100);


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
                            $myDisc = $item['cost_price'] * $item['discount'] / 100;
                            break;
                        case 'Â£':
                        case 'â‚¬':
                            $myDisc = $item['discount'];
                            break;
                        default:
                            $myDisc = 0;
                    }

		    $subtotal+=(($item['cost_price']-$myDisc)*$item['quantity']);
		}
		return $subtotal;
	}

	function get_total()
	{
		$total = 0;
		foreach($this->get_cart() as $item)
		{
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = $item['cost_price'] * $item['discount'] / 100;
                            break;
                        case '£':
                        case '¬':
                            $myDisc = $item['discount'];
                            break;
                        default:
                            $myDisc = 0;
                    }
                    $total+=(($item['cost_price']-$myDisc)*$item['quantity']);
		}

		foreach($this->get_taxes() as $tax)
		{
			$total+=$tax;
		}

		return $total;
	}
}
?>