<?php
class Branch extends Model
{
	/*Determines whether the given branch exists*/
	function exists($branch_ref)
	{
		$this->db->from('branches');
		$this->db->where('branch_ref',$branch_ref);
		$query = $this->db->get();

		return ($query->num_rows()==1);
	}

	/*Gets all branches*/
	function get_all()
	{
		$this->db->from('branches');
		$this->db->order_by("branch_name", "asc");
		return $this->db->get();
	}

	/*
	Gets information about a branch as an array.
	*/
	function get_info($branch_ref)
	{
		$query = $this->db->get_where('branches', array('branch_ref' => $branch_ref), 1);

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$fields = $this->db->list_fields('branches');
			$branch_obj = new stdClass;

			foreach ($fields as $field)
			{
				$branch_obj->$field='';
			}

			return $branch_obj;
		}
	}

	/*
	Get branches with specific ids
	*/
	function get_multiple_info($branch_refs)
	{
		$this->db->from('branches');
		$this->db->where_in('branch_ref',$branch_refs);
		$this->db->order_by("branch_name", "asc");
		return $this->db->get();
	}

	/*
	Inserts or updates a branch
	*/
	function save(&$branch_data,$branch_ref=false)
	{
		if (!$branch_ref or !$this->exists($branch_ref))
		{
			if ($this->db->insert('branches',$branch_data))
			{
				$branch_data['branch_ref']=$this->db->insert_id();
				return true;
			}

			return false;
		}

		$this->db->where('branch_ref', $branch_ref);
		return $this->db->update('branches',$branch_data);
	}

	/*
	Deletes one Branch (doesn't actually do anything)
	*/
	function delete($branch_ref)
	{
		return true;;
	}

	/*
	Deletes a list of branches (doesn't actually do anything)
	*/
	function delete_list($branch_refs)
	{
		return true;
 	}

	function get_search_suggestions($search)
	{
//log_message('debug', 'BRANCH SUGGESTIONS for :'.$search);
		$suggestions = array();
		$this->db->from('branches');
		$this->db->like('branch_name', $search);
		$this->db->where('deleted', 0);
		$this->db->order_by("branch_name", "asc");
		$by_branch = $this->db->get();
		foreach($by_branch->result() as $row)
		{
//log_message('debug', 'SUGGESTION:'.$row->branch_name);
			$suggestions[]=$row->branch_ref."|".$row->branch_name;
		}

		return $suggestions;
	}

}
?>
