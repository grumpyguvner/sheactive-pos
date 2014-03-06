<?php
class Item extends Model
{
	/*
	Determines if a given item_id is an item
	*/
	function exists($item_id)
	{
		$this->db->from('items');
		$this->db->where('item_id',$item_id);
		$this->db->where('deleted',0);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}
	/*
	Determines if a given item_id is an item
	*/
	function get_id_by_plu($plu)
	{
		$this->db->from('items');
                //Temporary fix to lose the check digit
                if (strlen($plu)==12){
//log_message('debug', "WHERE:"."(item_number LIKE '".$this->db->escape_like_str($plu)."%') and deleted=0");
                    $this->db->where("(item_number LIKE '".$this->db->escape_like_str($plu)."%' OR ean_upc LIKE '".$this->db->escape_like_str($plu)."%') and deleted=0");
                }else{
//log_message('debug', "WHERE:"."(item_number = '".$this->db->escape_like_str($plu)."') and deleted=0");
                    $this->db->where("(item_number = '".$this->db->escape_like_str($plu)."' OR ean_upc = '".$this->db->escape_like_str($plu)."') and deleted=0");
                }
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			return $row->item_id;
		}

		return false;
	}

	/*
	Returns all the items
	*/
//	function get_all($limit=0)
	function get_all($num=null, $offset=null)
	{
		$this->db->from('items');
		$this->db->join('locations', 'locations.location_id = items.location_id', "left");
		$this->db->select('CONCAT("<input type=\'checkbox\' id=\'", item_id,"\' value=\'", item_id,"\'/>"), CONCAT("<a href=\'items/view/",item_id,"/width:600\' class=\'thickbox\' title=\'edit item\'>", item_number, "</a>") AS item_number, CONCAT(name,"<br\>",description), cost_price, unit_price, supplierref, CONCAT("<a href=\'items/count_details/",item_id,"/width:600\' class=\'thickbox\' title=\'inventory history\'>", quantity, "</a>") AS inventory, reorder_level, reorder_quantity, CONCAT("<a href=\'items/move/",item_id,"/width:300\' class=\'thickbox\' title=\'move item\'>", IFNULL(location_ref,"UNKNOWN"), "</a>") AS location_ref', false);
		$this->db->where('items.deleted',0);
		$this->db->order_by("name", "asc");
//		return $this->db->get();
		return $this->db->get('', $num, $offset);
	}

	function get_all_filtered($low_inventory=0,$is_serialized=0,$no_description=0,$name="", $plu=null, $location=null,$num=null, $offset=null)
	{
                //Temporary fix to lose the ?
                if (strlen($plu)==14){
                    $plu = substr($plu, 0, -1);
                }

                //Temporary fix to lose the check digit
                if (strlen($plu)==13){
                    $plu = substr($plu, 0, -1);
                }

		$this->db->from('items');
		$this->db->join('locations', 'locations.location_id = items.location_id', "left");
		$this->db->select('CONCAT("<input type=\'checkbox\' id=\'", item_id,"\' value=\'", item_id,"\'/>"), CONCAT("<a href=\'items/view/",item_id,"/width:600\' class=\'thickbox\' title=\'edit item\'>", item_number, "</a>") AS item_number, CONCAT(name,"<br\>",description), cost_price, unit_price, supplierref, CONCAT("<a href=\'items/count_details/",item_id,"/width:600\' class=\'thickbox\' title=\'inventory history\'>", quantity, "</a>") AS inventory, reorder_level, reorder_quantity, CONCAT("<a href=\'items/move/",item_id,"/width:300\' class=\'thickbox\' title=\'move item\'>", IFNULL(location_ref,"UNKNOWN"), "</a>") AS location_ref', false);
		if ($low_inventory !=0 )
		{
			$this->db->where('quantity <=','reorder_level');
		}
		if ($is_serialized !=0 )
		{
			$this->db->where('is_serialized',1);
		}
		if ($no_description!=0 )
		{
			$this->db->where('description','');
		}
		if ($name!="" )
		{
			$this->db->like('name',$name);
		}
		if ($plu!=null)
		{
			$this->db->like('item_number',$plu);
			$this->db->orlike('ean_upc',$plu);
		}
		if ($location!=null)
		{
//			$this->db->where('items.location_id',$location);
			$this->db->where('location_ref',$location);
                }
		$this->db->where('items.deleted',0);
		$this->db->order_by("name", "asc");
//		return $this->db->get();
		return $this->db->get('', $num, $offset);
	}

	function get_all_filtered_count($low_inventory=0,$is_serialized=0,$no_description=0,$name="",$plu=null,$location=null)
	{
           
		$this->db->from('items');
		$this->db->join('locations', 'locations.location_id = items.location_id', "left");
		$this->db->select('CONCAT("<input type=\'checkbox\' id=\'", item_id,"\' value=\'", item_id,"\'/>"), CONCAT("<a href=\'items/view/",item_id,"/width:600\' class=\'thickbox\' title=\'edit item\'>", item_number, "</a>") AS item_number, CONCAT(name,"<br\>",description), cost_price, unit_price, supplierref, CONCAT("<a href=\'items/count_details/",item_id,"/width:600\' class=\'thickbox\' title=\'inventory history\'>", quantity, "</a>") AS inventory, CONCAT("<a href=\'items/move/",item_id,"/width:300\' class=\'thickbox\' title=\'move item\'>", IFNULL(location_ref,"UNKNOWN"), "</a>") AS location_ref', false);
		if ($low_inventory !=0 )
		{
			$this->db->where('quantity <=','reorder_level');
		}
		if ($is_serialized !=0 )
		{
			$this->db->where('is_serialized',1);
		}
		if ($no_description!=0 )
		{
			$this->db->where('description','');
		}
		if ($name!="" )
		{
			$this->db->like('name',$name);
		}
		if ($plu!=null)
		{
			$this->db->like('item_number',$plu);
			$this->db->orlike('ean_upc',$plu);
		}
		if ($location!=null)
		{
//			$this->db->where('items.location_id',$location);
			$this->db->where('location_ref',$location);
                }
		$this->db->where('items.deleted',0);
//		return $this->db->count_all_results('items');
		return $this->db->count_all_results('');
	}

	/*
	Gets information about a particular item
	*/
	function get_info($item_id)
	{
		$this->db->from('items');
		$this->db->where('item_id',$item_id);
		$this->db->where('deleted',0);
		
		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->db->list_fields('items');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}

	/*
	Get an item id given an item number
	*/
	function get_item_id($item_number)
	{
		$this->db->from('items');
		$this->db->where('item_number',$item_number);
		$this->db->where('deleted',0);

		$query = $this->db->get();

		if($query->num_rows()==1)
		{
			return $query->row()->item_id;
		}

		return false;
	}

	/*
	Gets information about multiple items
	*/
	function get_multiple_info($item_ids)
	{
		$this->db->from('items');
		$this->db->where_in('item_id',$item_ids);
		$this->db->where('deleted',0);
		$this->db->order_by("item", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates a item
	*/
	function save(&$item_data,$item_id=false)
	{
            //knock check digit off product code
            if (isset($item_data['item_number'])){
                if (strlen($item_data['item_number'])==13){
                    $item_data['item_number'] = substr($item_data['item_number'],0,-1);
                }
            }

            if (!$item_id or !$this->exists($item_id))
            {
                    if($this->db->insert('items',$item_data))
                    {
                            $item_data['item_id']=$this->db->insert_id();
                            return true;
                    }
                    return false;
            }

            $this->db->where('item_id', $item_id);
            return $this->db->update('items',$item_data);
	}

	/*
	Updates multiple items at once
	*/
	function update_multiple($item_data,$item_ids)
	{
		$this->db->where_in('item_id',$item_ids);
		return $this->db->update('items',$item_data);
	}

	/*
	Deletes one item
	*/
	function delete($item_id)
	{
		$this->db->where('item_id', $item_id);
		return $this->db->update('items', array('deleted' => 1));
	}

	/*
	Deletes a list of items
	*/
	function delete_list($item_ids)
	{
		$this->db->where_in('item_id',$item_ids);
		return $this->db->update('items', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find items
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

//		$this->db->select('CONCAT(name,description) AS name');
		$this->db->select('name');
		$this->db->distinct();
		$this->db->from('items');
		$this->db->like('name', $search);
		$this->db->where('deleted',0);
		$this->db->order_by("name", "asc");
		$by_name = $this->db->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->name;
//			$suggestions[]=$row->name." ".$row->description;
		}

		$this->db->select('category');
		$this->db->from('items');
		$this->db->where('deleted',0);
		$this->db->distinct();
		$this->db->like('category', $search);
		$this->db->order_by("category", "asc");
		$by_category = $this->db->get();
		foreach($by_category->result() as $row)
		{
			$suggestions[]=$row->category;
		}

		$this->db->from('items');
		$this->db->like('item_number', $search);
		$this->db->where('deleted',0);
		$this->db->order_by("item_number", "asc");
		$by_item_number = $this->db->get();
		foreach($by_item_number->result() as $row)
		{
			$suggestions[]=$row->item_number;
		}


		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	function get_item_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->db->from('items');
		$this->db->where('deleted',0);
		$this->db->like('name', $search);
		$this->db->order_by("name", "asc");
		$by_name = $this->db->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->item_id.'|'.$row->name." ".$row->description;
		}

		$this->db->from('items');
		$this->db->where('deleted',0);
		$this->db->like('item_number', $search);
		$this->db->order_by("item_number", "asc");
		$by_item_number = $this->db->get();
		foreach($by_item_number->result() as $row)
		{
			$suggestions[]=$row->item_id.'|'.$row->item_number;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	function get_category_suggestions($search)
	{
		$suggestions = array();
		$this->db->distinct();
		$this->db->select('category');
		$this->db->from('items');
		$this->db->like('category', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by("category", "asc");
		$by_category = $this->db->get();
		foreach($by_category->result() as $row)
		{
			$suggestions[]=$row->category;
		}

		return $suggestions;
	}

	/*
	Preform a search on items
	*/
	function search($search)
	{
		$this->db->from('items');
		$this->db->where("(name LIKE '%".$this->db->escape_like_str($search)."%' or 
		item_number LIKE '%".$this->db->escape_like_str($search)."%' or 
		category LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");
		$this->db->order_by("name", "asc");
		return $this->db->get();	
	}

	function get_categories()
	{
		$this->db->select('category');
		$this->db->from('items');
		$this->db->where('deleted',0);
		$this->db->distinct();
		$this->db->order_by("category", "asc");

		return $this->db->get();
	}
}
?>
