<?php
require_once ("interfaces/idata_controller.php");
require_once ("secure_area.php");
class Locations extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('locations');
	}
	
	function index()
	{
		$data['controller_name']=strtolower($this->uri->segment(1));
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_location_manage_table($this->Location->get_all(),$this);
		$this->load->view('locations/manage',$data);
	}

	function generate_barcodes($location_ids)
	{
		$result = array();

		$location_ids = explode(',', $location_ids);
		foreach ($location_ids as $location_id)
		{
			$location_info = $this->Location->get_info($location_id);

			$result[] = array('reference' =>$location_info->location_ref,
                                            'quantity' =>1,
                                            'id' =>$location_info->location_id,
                                            'comment'=>$location_info->location_comment);
		}

		$data['cart'] = $result;
		$this->load->view("locations/labels", $data);
	}
	
	/*
	Returns location table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_location_manage_table_data_rows($this->Location->search($search),$this);
		echo $data_rows;
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
//log_message('debug', 'BEGIN BRANCH SUGGESTION:');
		$suggestions = $this->Location->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	/*
	Loads the location edit form
	*/
	function view($location_id=-1)
	{
		$data['location_info']=$this->Location->get_info($location_id);
		$this->load->view("locations/form",$data);
	}
	
	/*
	Inserts/updates a location
	*/
	function save($location_id=-1)
	{
		$location_data=array(
		'location_comment'=>$this->input->post('location_comment'),
		'location_ref'=>$this->input->post('location_ref')=='' ? null:$this->input->post('location_ref'),
		);
		if($this->Location->save($location_data,$location_id))
		{
			//New location
			if($location_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('locations_successful_adding').' '.
				$location_data['location_ref'],'location_id'=>$location_data['location_id']));
			}
			else //previous location
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('locations_successful_updating').' '.
				$location_data['location_ref'],'location_id'=>$location_id));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('locations_error_adding_updating').' '.
			$location_data['location_ref'],'location_id'=>-1));
		}
	}
	
	/*
	This deletes locations from the locations table
	*/
	function delete()
	{
		$locations_to_delete=$this->input->post('ids');
		
		if($this->Location->delete_list($locations_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('locations_successful_deleted').' '.
			count($locations_to_delete).' '.$this->lang->line('locations_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('locations_cannot_be_deleted')));
		}
	}
	
	/*
	Gets one row for a location manage table. This is called using AJAX to update one row.
	*/
	function get_row()
	{
		$location_id = $this->input->post('row_id');
		$data_row=get_location_data_row($this->Location->get_info($location_id),$this);
		echo $data_row;
	}
	
	/*
	get the width for the add/edit form
	*/
	function get_form_width()
	{			
		return 360;
	}
}
?>