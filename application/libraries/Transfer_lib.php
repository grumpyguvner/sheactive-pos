<?php
class Transfer_lib
{
	var $CI;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('cartTran'))
			$this->set_cart(array());

		return $this->CI->session->userdata('cartTran');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cartTran',$cart_data);
	}

	function get_branch()
	{
		if(!$this->CI->session->userdata('branch'))
			$this->set_branch(-1);

		return $this->CI->session->userdata('branch');
	}

	function set_branch($branch_ref)
	{
		$this->CI->session->set_userdata('branch',$branch_ref);
	}

	function get_mode()
	{
		if(!$this->CI->session->userdata('tran_mode'))
			$this->set_mode('receive');

		return $this->CI->session->userdata('tran_mode');
	}

	function set_mode($mode)
	{
		$this->CI->session->set_userdata('tran_mode',$mode);
	}

	function add_plu($plu,$quantity=1,$description=null,$serialnumber=null,$location_id=null)
	{
//log_message('debug', "PLU:".$plu);
            $item_id = $this->CI->Item->get_id_by_plu($plu);
//log_message('debug', "ITEM:".$item_id);
            return $this->add_item($item_id,$quantity);
	}

	function add_item($item_id,$quantity=1,$description=null,$serialnumber=null,$location_id=null)
	{
		//make sure item exists in database.
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		//Get items in the transfer so far.
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
                    //add the new item to the begining of the array so that$item['quantity']
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
                            'cost_price'=>$this->CI->Item->get_info($item_id)->cost_price,
                            'unit_price'=>$this->CI->Item->get_info($item_id)->unit_price,
                            'retail_price'=>$this->CI->Item->get_info($item_id)->retail_price,
                            'current_quantity'=>$this->CI->Item->get_info($item_id)->quantity,
                    	    'ean_upc'=>$this->CI->Item->get_info($item_id)->ean_upc,
                            'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
                            'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
                            'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
                            'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
			    'location_id'=>$this->CI->Item->get_info($item_id)->location_id,
			    'location'=>$this->CI->Item->get_info($item_id)->location_id!=null ? $this->CI->Location->get_info($this->CI->Item->get_info($item_id)->location_id)->location_ref:'NOWHERE',
                            'quantity'=>$quantity
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
                                'cost_price'=>$item['cost_price'],
                                'unit_price'=>$item['unit_price'],
                                'retail_price'=>$item['retail_price'],
                                'current_quantity'=>$item['current_quantity'],
                        		'ean_upc'=>$item['ean_upc'],
                                'description'=>$item['description'],
                                'serialnumber'=>$item['serialnumber'],
                                'allow_alt_description'=>$item['allow_alt_description'],
                                'is_serialized'=>$item['is_serialized'],
	                        'location_id'=>$item['location_id'],
	                        'location'=>$item['location'],
                                'quantity'=>$item['quantity']
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

	function edit_item($line,$description,$ean_upc,$serialnumber,$quantity)
	{
		$items = $this->get_cart();
		if(isset($items[$line]))
		{
			$items[$line]['description'] = $description;
			$items[$line]['ean_upc'] = $ean_upc;
			$items[$line]['serialnumber'] = $serialnumber;
			$items[$line]['quantity'] = $quantity;
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

	function is_valid_receipt($receipt_transfer_id)
	{
		//TRAN #
		$pieces = explode(' ',$receipt_transfer_id);

		if(count($pieces)==2 && $pieces[1]=="TRAN")
		{
			return $this->CI->Transfer->exists($pieces[1]);
		}

		return false;
	}

	function return_entire_transfer($receipt_transfer_id)
	{
		//TRAN #
		$pieces = explode(' ',$receipt_transfer_id);
		$transfer_id = $pieces[1];

		$this->empty_cart();
		$this->delete_branch();

		foreach($this->CI->Transfer->get_transfer_items($transfer_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_transfered,$row->description,$row->serialnumber);
		}
		$this->set_branch($this->CI->Transfer->get_branch($transfer_id)->branch_ref);
	}

	function copy_entire_transfer($transfer_id)
	{
		$this->empty_cart();
		$this->delete_branch();

		foreach($this->CI->Transfer->get_transfer_items($transfer_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_transfered,$row->description,$row->serialnumber);
		}
		$this->set_branch($this->CI->Transfer->get_branch($transfer_id)->branch_ref);

	}

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cartTran');
	}

	function delete_branch()
	{
		$this->CI->session->unset_userdata('branch');
	}

	function clear_mode()
	{
		$this->CI->session->unset_userdata('transfer_mode');
	}

	function clear_all()
	{
		$this->clear_mode();
		$this->empty_cart();
		$this->delete_branch();
	}

}
?>