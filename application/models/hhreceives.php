<?php
class HHReceives extends Model
{
	/*Determines whether the given record exists*/
	function exists($device, $item_number, $timestamp)
	{
		$this->db->from('hhreceives');
		$this->db->where('device',$device);
		$this->db->where('item_number',$item_number);
		$this->db->where('timestamp',$timestamp);
		$query = $this->db->get();
		foreach($query->result() as $row)
		{
			return $row->hhreceives_id;
		}

		return false;
	}

	/*Determines whether the given record has been processed*/
	function processed($hhreceives_id)
	{
		$this->db->from('hhreceives');
		$this->db->where('hhreceives_id',$hhreceives_id);
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
	function insert($hhreceives_data)
	{
            if ($this->exists($hhreceives_data['device'],$hhreceives_data['item_number'],$hhreceives_data['timestamp']))
                    return false;

            if ($this->db->insert('hhreceives',$hhreceives_data))
            {
                    $hhreceives_data['hhreceives_id']=$this->db->insert_id();
                    return true;
            }

            return false;
	}

	/*
	Saves updates to record
	*/
	function save($hhreceives_data,$hhreceives_id=false)
	{
            if (!$hhreceives_id)
            {
                return $this->insert($hhreceives_data);
            }else{
                $this->db->where('hhreceives_id', $hhreceives_id);
                return $this->db->update('hhreceives',$hhreceives_data);
            }
	}

}
?>
