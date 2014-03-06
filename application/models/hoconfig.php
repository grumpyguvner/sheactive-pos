<?php
class HOconfig extends Model
{
	
	function exists($key)
	{
		$this->db->from('headoffice');
		$this->db->where('headoffice.key',$key);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all()
	{
		$this->db->from('headoffice');
		$this->db->order_by("key", "asc");
		return $this->db->get();		
	}
	
	function get($key)
	{
		$query = $this->db->get_where('headoffice', array('key' => $key), 1);
		
		if($query->num_rows()==1)
		{
			return $query->row()->value;
		}
		
		return "";
		
	}
	
	function save($key,$value)
	{
		$headoffice_data=array(
		'key'=>$key,
		'value'=>$value
		);
				
		if (!$this->exists($key))
		{
			return $this->db->insert('headoffice',$headoffice_data);
		}
		
		$this->db->where('key', $key);
		return $this->db->update('headoffice',$headoffice_data);
	}
	
	function batch_save($data)
	{
		$success=true;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		foreach($data as $key=>$value)
		{
			if(!$this->save($key,$value))
			{
				$success=false;
				break;
			}
		}
		
		$this->db->trans_complete();		
		return $success;
		
	}
		
	function delete($key)
	{
		return $this->db->delete('headoffice', array('key' => $key));
	}
	
	function delete_all()
	{
		return $this->db->empty_table('headoffice');
	}
}

?>