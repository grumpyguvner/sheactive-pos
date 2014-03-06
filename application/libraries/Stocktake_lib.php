<?php
class Stocktake_lib
{
	var $CI;

  	function __construct()
	{
		$this->CI =& get_instance();
	}

	function get_cart()
	{
		if(!$this->CI->session->userdata('cartStock'))
			$this->set_cart(array());

		return $this->CI->session->userdata('cartStock');
	}

	function set_cart($cart_data)
	{
		$this->CI->session->set_userdata('cartStock',$cart_data);
	}

	function get_location()
	{
		if(!$this->CI->session->userdata('location'))
			$this->set_location(-1);

		return $this->CI->session->userdata('location');
	}

	function set_location($location_id)
	{
		$this->CI->session->set_userdata('location',$location_id);
		$this->empty_cart();
                $items = $this->CI->Location->get_location_contents($location_id);
		foreach ($items as $item)
		{
//echo "adding ".$item->item_id."<br/>";
                    $this->add_item($item->item_id,0);
		}
	}

	function add_plu($plu,$quantity=1,$description=null,$serialnumber=null)
	{
            $item_id = $this->CI->Item->get_id_by_plu($plu);
//            return $this->add_item($item_id,$quantity,$discount,$discount_type,$discount_reason,$price,$tax,$description,$serialnumber);
            return $this->add_item($item_id,$quantity);
        }

	function add_item($item_id,$quantity=1,$description=null,$serialnumber=null)
	{
		//make sure item exists in database.
		if(!$this->CI->Item->exists($item_id))
		{
			//try to get item id given an item_number
			$item_id = $this->CI->Item->get_item_id($item_id);

			if(!$item_id)
				return false;
		}

		//Get items in the stocktake so far.
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
                            'unit_price'=>$this->CI->Item->get_info($item_id)->unit_price,
                            'ean_upc'=>$this->CI->Item->get_info($item_id)->ean_upc,
                            'description'=>$description!=null ? $description: $this->CI->Item->get_info($item_id)->description,
                            'serialnumber'=>$serialnumber!=null ? $serialnumber: '',
                            'allow_alt_description'=>$this->CI->Item->get_info($item_id)->allow_alt_description,
                            'is_serialized'=>$this->CI->Item->get_info($item_id)->is_serialized,
                            'location_id'=>$this->CI->Item->get_info($item_id)->location_id,
                            'location'=>$this->CI->Item->get_info($item_id)->location_id!=null ? $this->CI->Location->get_info($this->CI->Item->get_info($item_id)->location_id)->location_ref:'NOWHERE',
                            'current_quantity'=>$this->CI->Item->get_info($item_id)->quantity,
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
                                'unit_price'=>$item['unit_price'],
                                'ean_upc'=>$item['ean_upc'],
                                'description'=>$item['description'],
                                'serialnumber'=>$item['serialnumber'],
                                'allow_alt_description'=>$item['allow_alt_description'],
                                'is_serialized'=>$item['is_serialized'],
                                'location_id'=>$item['location_id'],
                                'location'=>$item['location'],
                                'current_quantity'=>$item['current_quantity'],
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

	function delete_item($line)
	{
		$items=$this->get_cart();
		unset($items[$line]);
		$this->set_cart($items);
	}

	function empty_cart()
	{
		$this->CI->session->unset_userdata('cartStock');
	}

	function delete_location()
	{
		$this->CI->session->unset_userdata('location');
	}

	function clear_all()
	{
		$this->empty_cart();
		$this->delete_location();
	}
	function copy_entire_stocktake($stocktake_id)
	{
		$this->empty_cart();
		$this->delete_location();

		foreach($this->CI->Stocktake->get_stocktake_items($stocktake_id)->result() as $row)
		{
			$this->add_item($row->item_id,$row->quantity_counted,$row->description,$row->serialnumber);
		}
		$this->set_location($this->CI->Stocktake->get_location($stocktake_id)->location_id);

	}

}
?>
