<?php
require_once ("interfaces/idata_controller.php");
require_once ("secure_area.php");
class Branches extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('branches');
	}
	
	function index()
	{
		$data['controller_name']=strtolower($this->uri->segment(1));
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_branch_manage_table($this->Branch->get_all(),$this);
		$this->load->view('branches/manage',$data);
	}
	
	/*
	Returns branch table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_branch_manage_table_data_rows($this->Branch->search($search),$this);
		echo $data_rows;
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
//log_message('debug', 'BEGIN BRANCH SUGGESTION:');
		$suggestions = $this->Branch->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	/*
	Loads the branch edit form
	*/
	function view($branch_ref=-1)
	{
		$data['branch_info']=$this->Branch->get_info($branch_ref);
		$this->load->view("branches/form",$data);
	}
	
	/*
	Inserts/updates a branch
	*/
	function save($branch_ref=-1)
	{
		$branch_data=array(
		'branch_name'=>$this->input->post('branch_name'),
		'branch_ref'=>$this->input->post('branch_ref')=='' ? null:$this->input->post('branch_ref'),
		);
		if($this->Branch->save($branch_data,$branch_ref))
		{
			//New branch
			if($branch_ref==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('branches_successful_adding').' '.
				$branch_data['branch_name'],'branch_ref'=>$branch_data['branch_ref']));
			}
			else //previous branch
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('branches_successful_updating').' '.
				$branch_data['branch_name'],'branch_ref'=>$branch_ref));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('branches_error_adding_updating').' '.
			$branch_data['branch_name'],'branch_ref'=>-1));
		}
	}
	
	/*
	This deletes branches from the branches table
	*/
	function delete()
	{
		$branches_to_delete=$this->input->post('ids');
		
		if($this->Branch->delete_list($branches_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('branches_successful_deleted').' '.
			count($branches_to_delete).' '.$this->lang->line('branches_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('branches_cannot_be_deleted')));
		}
	}
	
	/*
	Gets one row for a branch manage table. This is called using AJAX to update one row.
	*/
	function get_row()
	{
		$branch_ref = $this->input->post('row_id');
		$data_row=get_branch_data_row($this->Branch->get_info($branch_ref),$this);
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