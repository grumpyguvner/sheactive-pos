<?php
class Location extends Model
{
	/*Determines whether the given location exists*/
	function exists($location_id)
	{
		$this->db->from('locations');
		$this->db->where('location_id',$location_id);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*Gets all locations*/
	function get_all()
	{
		$this->db->from('locations');
		$this->db->order_by("location_ref", "asc");
		return $this->db->get();
	}

	/*
	Gets information about a location as an array.
	*/
	function get_info($location_id)
	{
		$query = $this->db->get_where('locations', array('location_id' => $location_id), 1);

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$fields = $this->db->list_fields('locations');
			$location_obj = new stdClass;

			foreach ($fields as $field)
			{
				$location_obj->$field='';
			}

			return $location_obj;
		}
	}

	/*
	Get locations with specific ids
	*/
	function get_multiple_info($location_ids)
	{
		$this->db->from('locations');
		$this->db->where_in('location_id',$location_ids);
		$this->db->order_by("location_ref", "asc");
		return $this->db->get();
	}

	/*
	Get contents of specific location
	*/
	function get_location_contents($location_id)
	{
		$items = array();
		$this->db->from('items');
		$this->db->where('location_id = '.$location_id);
		$this->db->order_by("item_number", "asc");
		$result = $this->db->get();
		foreach($result->result() as $row)
		{
//			$items[]=$row->item_number;
			$items[]=$row;
		}
		return $items;
	}

        /*
	Preform a search on locations
	*/
	function search($search)
	{
		$this->db->from('locations');
		$this->db->where("(location_ref LIKE '%".$this->db->escape_like_str($search)."%') and deleted=0");
		$this->db->order_by("location_ref", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates a location
	*/
	function save($location_data,$location_id=false)
	{
		if (!$location_id or !$this->exists($location_id))
		{
			if ($this->db->insert('locations',$location_data))
			{
				$location_data['location_id']=$this->db->insert_id();
				return true;
			}

			return false;
		}

		$this->db->where('location_id', $location_id);
		return $this->db->update('locations',$location_data);
	}

	/*
	Deletes one Location (doesn't actually do anything)
	*/
	function delete($location_id)
	{
		return true;;
	}

	/*
	Deletes a list of locations (doesn't actually do anything)
	*/
	function delete_list($location_ids)
	{
		return true;
 	}

	function get_search_suggestions($search)
	{
//log_message('debug', 'BRANCH SUGGESTIONS for :'.$search);
		$suggestions = array();
		$this->db->from('locations');
		$this->db->like('location_ref', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by("location_ref", "asc");
		$by_location = $this->db->get();
		foreach($by_location->result() as $row)
		{
//log_message('debug', 'SUGGESTION:'.$row->location_ref);
			$suggestions[]=$row->location_id."|".$row->location_ref;
		}

		return $suggestions;
	}
}
?>
