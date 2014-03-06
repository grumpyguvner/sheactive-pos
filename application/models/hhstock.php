<?php
class HHStock extends Model
{
	/*Determines whether the given record exists*/
	function exists($device, $item_number, $timestamp)
	{
		$this->db->from('hhstock');
		$this->db->where('device',$device);
		$this->db->where('item_number',$item_number);
		$this->db->where('timestamp',$timestamp);
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			return $row->hhstock_id;
		}

		return false;
	}

	/*Determines whether the given record has been processed*/
	function processed($hhstock_id)
	{
		$this->db->from('hhstock');
		$this->db->where('hhstock_id',$hhstock_id);
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			return $row->processed;
		}

		return false;
	}

	/*
	Inserts a record
	*/
	function insert($hhstock_data)
	{
            if ($this->exists($hhstock_data['device'],$hhstock_data['item_number'],$hhstock_data['timestamp']))
                    return false;

            if ($this->db->insert('hhstock',$hhstock_data))
            {
                    $hhstock_data['hhstock_id']=$this->db->insert_id();
                    return true;
            }

            return false;
	}

	/*
	Saves updates to record
	*/
	function save($hhstock_data,$hhstock_id=false)
	{
            if (!$hhstock_id)
            {
                return $this->insert($hhstock_data);
            }else{
                $this->db->where('hhstock_id', $hhstock_id);
                return $this->db->update('hhstock',$hhstock_data);
            }
	}

}
?>
