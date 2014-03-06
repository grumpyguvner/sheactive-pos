<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Items extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('items');
	}

	function index()
	{
		$data['controller_name']=strtolower($this->uri->segment(1));
		$data['form_width']=$this->get_form_width();
//		$data['manage_table']=get_items_manage_table($this->Item->get_all(),$this);
//		$this->load->view('items/manage',$data);

    // load pagination class
    $this->load->library('pagination');
    $config['base_url'] = base_url().'items/index/';
    $config['total_rows'] = $this->db->count_all('items');
    $config['per_page'] = '20';
    $config['full_tag_open'] = '<p>';
    $config['full_tag_close'] = '</p>';

    $this->pagination->initialize($config);

    //load the model and get results
    $this->load->model('Item');
//    $data['results'] = $this->Item->get_items($config['per_page'],$this->uri->segment(3));
    $data['results'] = $this->Item->get_all($config['per_page'],$this->uri->segment(3));

    // load the HTML Table Class
    $this->load->library('table');
    $this->table->set_heading('<input type="checkbox" id="select_all" />',
	$this->lang->line('items_item_number'),
	$this->lang->line('items_name'),
	$this->lang->line('items_cost_price'),
	$this->lang->line('items_unit_price'),
	$this->lang->line('items_supplierref'),
	$this->lang->line('items_quantity'),
	$this->lang->line('items_reorder_level'),
	$this->lang->line('items_reorder_quantity'),
	$this->lang->line('items_location_ref')
	);
    $template=array(
      'table_open'=>  '<table class="tablesorter" id="sortable_table">'
    );
    $this->table->set_template($template);
//    	$table='<table class="tablesorter" id="sortable_table">';

    // load the view
//    $this->load->view('items/items_view', $data);
    $this->load->view('items/manage', $data);


        }

	function refresh()
	{
            $plu=$this->input->post('plu');
            $search=$this->Item->get_info($this->input->post('item'))->name;
            $data['location']=$this->input->post('location');
            $location=$this->input->post('location');

            $low_inventory=$this->input->post('low_inventory');
            $is_serialized=$this->input->post('is_serialized');
            $no_description=$this->input->post('no_description');

            $data['search_section_state']=$this->input->post('search_section_state');
            $data['search']=$this->input->post('search');
            $data['low_inventory']=$this->input->post('low_inventory');
            $data['is_serialized']=$this->input->post('is_serialized');
            $data['no_description']=$this->input->post('no_description');
            $data['controller_name']=strtolower($this->uri->segment(1));
            $data['form_width']=$this->get_form_width();
//		$data['manage_table']=get_items_manage_table($this->Item->get_all_filtered($low_inventory,$is_serialized,$no_description,$search),$this);
//		$this->load->view('items/manage',$data);

    $perPage = 20;
    $offset = (int) $this->uri->segment(3) > 0;
    
    //load the model and get results
    $this->load->model('Item');
//    $data['results'] = $this->Item->get_items($config['per_page'],$this->uri->segment(3));
    $data['results'] = $this->Item->get_all_filtered($low_inventory,$is_serialized,$no_description,$search,$plu,$location,$perPage,$offset);

    // load pagination class
    $this->load->library('pagination');
    $config['base_url'] = base_url().'items/refresh/';
    $config['per_page'] = $perPage;
    $config['full_tag_open'] = '<p>';
    $config['full_tag_close'] = '</p>';
    $config['total_rows'] = $this->Item->get_all_filtered_count($low_inventory,$is_serialized,$no_description,$search,$plu,$location);

    $this->pagination->initialize($config);

    // load the HTML Table Class
    $this->load->library('table');
    $this->table->set_heading('<input type="checkbox" id="select_all" />',
	$this->lang->line('items_item_number'),
	$this->lang->line('items_name'),
	$this->lang->line('items_cost_price'),
	$this->lang->line('items_unit_price'),
	$this->lang->line('items_supplierref'),
	$this->lang->line('items_quantity'),
	$this->lang->line('items_reorder_level'),
	$this->lang->line('items_reorder_quantity'),
	$this->lang->line('items_location_ref')
	);
    $template=array(
      'table_open'=>  '<table class="tablesorter" id="sortable_table">'
    );
    $this->table->set_template($template);
//    	$table='<table class="tablesorter" id="sortable_table">';

    // load the view
//    $this->load->view('items/items_view', $data);
    $this->load->view('items/manage', $data);




        }

	function find_item_info()
	{
		$item_number=$this->input->post('scan_item_number');
		echo json_encode($this->Item->find_item_info($item_number));
	}

	function search()
	{
//		$search=$this->input->post('search');
//		$data_rows=get_items_manage_table_data_rows($this->Item->search($search),$this);
//		echo $data_rows;


		$search=$this->input->post('search');
		$low_inventory=$this->input->post('low_inventory');
		$is_serialized=$this->input->post('is_serialized');
		$no_description=$this->input->post('no_description');

		$data['search_section_state']=$this->input->post('search_section_state');
		$data['search']=$this->input->post('search');
		$data['low_inventory']=$this->input->post('low_inventory');
		$data['is_serialized']=$this->input->post('is_serialized');
		$data['no_description']=$this->input->post('no_description');
		$data['controller_name']=strtolower($this->uri->segment(1));
		$data['form_width']=$this->get_form_width();
//		$data['manage_table']=get_items_manage_table($this->Item->get_all_filtered($low_inventory,$is_serialized,$no_description,$search),$this);
//		$this->load->view('items/manage',$data);

    // load pagination class
    $this->load->library('pagination');
    $config['base_url'] = base_url().'items/index/';
    $config['total_rows'] = $this->db->count_all('items');
    $config['per_page'] = '20';
    $config['full_tag_open'] = '<p>';
    $config['full_tag_close'] = '</p>';

    $this->pagination->initialize($config);

    //load the model and get results
    $this->load->model('Item');
//    $data['results'] = $this->Item->get_items($config['per_page'],$this->uri->segment(3));
    $data['results'] = $this->Item->get_all_filtered($low_inventory,$is_serialized,$no_description,$search,$config['per_page'],$this->uri->segment(3));

    // load the HTML Table Class
    $this->load->library('table');
    $this->table->set_heading('<input type="checkbox" id="select_all" />',
	$this->lang->line('items_item_number'),
	$this->lang->line('items_name'),
	$this->lang->line('items_category'),
	$this->lang->line('items_cost_price'),
	$this->lang->line('items_unit_price'),
	$this->lang->line('items_quantity'),
	$this->lang->line('items_reorder_level'),
	$this->lang->line('items_reorder_quantity'),
	'&nbsp',
	'Inventory'//Ramel Inventory Tracking
	);
//    	$table='<table class="tablesorter" id="sortable_table">';

    // load the view
//    $this->load->view('items/items_view', $data);
    $this->load->view('items/manage', $data);




	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Item->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest_category()
	{
		$suggestions = $this->Item->get_category_suggestions($this->input->post('q'));
		echo implode("\n",$suggestions);
	}

	function get_row()
	{
		$item_id = $this->input->post('row_id');
		$data_row=get_item_data_row($this->Item->get_info($item_id),$this);
		echo $data_row;
	}

	function view($item_id=-1)
	{
		$data['item_info']=$this->Item->get_info($item_id);
		$data['item_tax_info']=$this->Item_taxes->get_info($item_id);
		$suppliers = array('' => $this->lang->line('items_none'));
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['company_name'] .' ('.$row['first_name'] .' '. $row['last_name'].')';
		}

		$data['suppliers']=$suppliers;
		$data['selected_supplier'] = $this->Item->get_info($item_id)->supplier_id;

                $locations = array('' => $this->lang->line('items_none'));
		foreach($this->Location->get_all()->result_array() as $row)
		{
			$locations[$row['location_id']] = $row['location_ref'];
		}

		$data['locations']=$locations;
		$data['location_id'] = $this->Item->get_info($item_id)->location_id;
		$data['selected_location'] = $this->Location->get_info($data['location_id'])->location_ref;

		$data['default_tax_1_rate']=($item_id==-1) ? $this->Appconfig->get('default_tax_1_rate') : '';
		$data['default_tax_2_rate']=($item_id==-1) ? $this->Appconfig->get('default_tax_2_rate') : '';
		$this->load->view("items/form",$data);
	}

	function move($item_id=-1)
	{
		$data['item_info']=$this->Item->get_info($item_id);

                $locations = array('' => $this->lang->line('items_none'));
		foreach($this->Location->get_all()->result_array() as $row)
		{
			$locations[$row['location_id']] = $row['location_ref'];
		}

		$data['locations']=$locations;
		$data['location_id'] = $this->Item->get_info($item_id)->location_id;
		$data['selected_location'] = $this->Location->get_info($data['location_id'])->location_ref;

		$this->load->view("items/move",$data);
	}
	
	//Ramel Inventory Tracking
	function inventory($item_id=-1)
	{
		$data['item_info']=$this->Item->get_info($item_id);
		$this->load->view("items/inventory",$data);
	}
	
	function count_details($item_id=-1)
	{
		$data['item_info']=$this->Item->get_info($item_id);
		$this->load->view("items/count_details",$data);
	} //------------------------------------------- Ramel

	function generate_barcodes($item_ids)
	{
		$result = array();

		$item_ids = explode(',', $item_ids);
		foreach ($item_ids as $item_id)
		{
			$item_info = $this->Item->get_info($item_id);

			$result[] = array('quantity' =>1,
                                            'name' =>$item_info->name,
                                            'description' =>$item_info->description,
                                            'item_number'=>$item_info->item_number);
		}

		$data['cart'] = $result;
                $this->load->view("receivings/labels", $data);
	}
	function generate_barcodes_prices($item_ids)
	{
		$result = array();

		$item_ids = explode(',', $item_ids);
		foreach ($item_ids as $item_id)
		{
			$item_info = $this->Item->get_info($item_id);

			$result[] = array('quantity' =>1,
                                            'name' =>$item_info->name,
                                            'retail_price'=>$item_info->retail_price,
                                            'description' =>$item_info->description,
                                            'item_number'=>$item_info->item_number);
		}

		$data['cart'] = $result;
                $this->load->view("transfers/labels", $data);
	}

	function bulk_edit()
	{
		$data = array();
		$suppliers = array('' => $this->lang->line('items_none'));
		foreach($this->Supplier->get_all()->result_array() as $row)
		{
			$suppliers[$row['person_id']] = $row['first_name'] .' '. $row['last_name'];
		}
		$data['suppliers'] = $suppliers;
		$locations = array('' => $this->lang->line('items_none'));
		foreach($this->Location->get_all()->result_array() as $row)
		{
			$locations[$row['location_id']] = $row['location_ref'];
		}
		$data['locations'] = $locations;
		$data['allow_alt_desciption_choices'] = array(
			''=>$this->lang->line('items_do_nothing'), 
			1 =>$this->lang->line('items_change_all_to_allow_alt_desc'),
			0 =>$this->lang->line('items_change_all_to_not_allow_allow_desc'));
				
		$data['serialization_choices'] = array(
			''=>$this->lang->line('items_do_nothing'), 
			1 =>$this->lang->line('items_change_all_to_serialized'),
			0 =>$this->lang->line('items_change_all_to_unserialized'));
		$this->load->view("items/form_bulk", $data);
	}

	function save_location($item_id=-1)
	{
//log_message('debug', "item_id=".$item_id);
//log_message('debug', "location_id=".$this->input->post('location_id'));
                        
		$item_data = array(
		'location_id'=>$this->input->post('location_id')=='' ? null:$this->input->post('location_id')
		);
		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
                $location=$this->Location->get_info($this->input->post('location_id'));

		if($this->Item->save($item_data,$item_id))
		{
                        $inv_data = array
                        (
                                'trans_date'=>date('Y-m-d H:i:s'),
                                'trans_items'=>$item_id,
                                'trans_user'=>$employee_id,
                                'trans_comment'=>"Moved to ".$location->location_ref,
                                'trans_inventory'=>0
                        );
                        $this->Inventory->insert($inv_data);
//                        echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_moving'),'item_id'=>$item_id));
                        echo json_encode(array('success'=>true,'message'=>'items_successful_moving','item_id'=>$item_id));
		}
		else//failure
		{
                        echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_error_moving'),'item_id'=>$item_id));
		}

	}

	function save($item_id=-1)
	{
		$item_data = array(
		'name'=>$this->input->post('name'),
		'ean_upc'=>$this->input->post('ean_upc'),
		'description'=>$this->input->post('description'),
		'category'=>$this->input->post('category'),
		'supplier_id'=>$this->input->post('supplier_id')=='' ? null:$this->input->post('supplier_id'),
		'item_number'=>$this->input->post('item_number')=='' ? null:$this->input->post('item_number'),
		'cost_price'=>$this->input->post('cost_price'),
		'retail_price'=>$this->input->post('retail_price'),
		'unit_price'=>$this->input->post('unit_price'),
		'quantity'=>$this->input->post('quantity'),
//		'location_id'=>$this->input->post('location_id')=='' ? null:$this->input->post('location_id'),
		'reorder_level'=>$this->input->post('reorder_level'),
		'reorder_quantity'=>$this->input->post('reorder_quantity'),
		'allow_alt_description'=>$this->input->post('allow_alt_description'),
		'is_serialized'=>$this->input->post('is_serialized')
		);
                //this was saving a 0 instead of the new ref??
//		'supplierref'=>$this->input->post('supplierref'),
		
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);

		if($this->Item->save($item_data,$item_id))
		{
			//New item
			if($item_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_adding').' '.
				$item_data['name'],'item_id'=>$item_data['item_id']));
				$item_id = $item_data['item_id'];
			}
			else //previous item
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_updating').' '.
				$item_data['name'],'item_id'=>$item_id));
			}
			
			$inv_data = array
			(
				'trans_date'=>date('Y-m-d H:i:s'),
				'trans_items'=>$item_id,
				'trans_user'=>$employee_id,
				'trans_comment'=>$this->lang->line('items_manually_editing_of_quantity'),
				'trans_inventory'=>$cur_item_info ? $this->input->post('quantity') - $cur_item_info->quantity : $this->input->post('quantity')
			);
			$this->Inventory->insert($inv_data);
			$items_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k] );
				}
			}
			$this->Item_taxes->save($items_taxes_data, $item_id);
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_error_adding_updating').' '.
			$item_data['name'],'item_id'=>-1));
		}

	}
	
	//Ramel Inventory Tracking
	function save_inventory($item_id=-1)
	{	
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$cur_item_info = $this->Item->get_info($item_id);
		$inv_data = array
		(
			'trans_date'=>date('Y-m-d H:i:s'),
			'trans_items'=>$item_id,
			'trans_user'=>$employee_id,
			'trans_comment'=>$this->input->post('trans_comment'),
			'trans_inventory'=>$this->input->post('newquantity')
		);
		$this->Inventory->insert($inv_data);
		
		//Update stock quantity
		$item_data = array(
		'quantity'=>$cur_item_info->quantity + $this->input->post('newquantity')
		);
		if($this->Item->save($item_data,$item_id))
		{			
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_updating').' '.
			$cur_item_info->name,'item_id'=>$item_id));
			
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_error_adding_updating').' '.
			$cur_item_info->name,'item_id'=>-1));
		}

	}//---------------------------------------------------------------------Ramel

	function bulk_update()
	{
		$items_to_update=$this->input->post('item_ids');
		$item_data = array();

		foreach($_POST as $key=>$value)
		{
			//These fields are nullable, so treat it differently
			if ($key == 'supplier_id' || $key == 'location_id')
			{
				$item_data["$key"]=$value == '' ? null : $value;
			}
			elseif($value!='' and !(in_array($key, array('item_ids', 'tax_names', 'tax_percents'))))
			{
				$item_data["$key"]=$value;
			}
		}

		//Item data could be empty if tax information is being updated
		if(empty($item_data) || $this->Item->update_multiple($item_data,$items_to_update))
		{
			$items_taxes_data = array();
			$tax_names = $this->input->post('tax_names');
			$tax_percents = $this->input->post('tax_percents');
			for($k=0;$k<count($tax_percents);$k++)
			{
				if (is_numeric($tax_percents[$k]))
				{
					$items_taxes_data[] = array('name'=>$tax_names[$k], 'percent'=>$tax_percents[$k] );
				}
			}
			$this->Item_taxes->save_multiple($items_taxes_data, $items_to_update);

			echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_bulk_edit')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_error_updating_multiple')));
		}
	}

	function delete()
	{
		$items_to_delete=$this->input->post('ids');

		if($this->Item->delete_list($items_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('items_successful_deleted').' '.
			count($items_to_delete).' '.$this->lang->line('items_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('items_cannot_be_deleted')));
		}
	}

	/**
	 * Display form: Import data from an excel file
	 * @author: Nguyen OJB
	 * @since: 10.1
	 */
	function excel_import()
	{
		$this->load->view("items/excel_import", null);
	}

	/**
	 * Read data from excel file -> save it to databse
	 * @author: Nguyen OJB
	 * @since: 10.1
	 */
	function do_excel_import()
	{
		$msg = "do_excel_import";
		$failCodes = null;
		$successCode = null;
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = $this->lang->line('items_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return ;
		}
		else
		{
			$this->load->library('spreadsheetexcelreader');
			$this->spreadsheetexcelreader->store_extended_info = false;
			$success = $this->spreadsheetexcelreader->read($_FILES['file_path']['tmp_name']);

			$rowCount = $this->spreadsheetexcelreader->rowcount(0);
			if($rowCount > 2){
				for($i = 3; $i<=$rowCount; $i++){
					$item_code = $this->spreadsheetexcelreader->val($i, 'A');
					$item_id = $this->Item->get_item_id($item_code);
					$item_data = array(
					'name'			=>	$this->spreadsheetexcelreader->val($i, 'B'),
					'description'	=>	$this->spreadsheetexcelreader->val($i, 'K'),
					'category'		=>	$this->spreadsheetexcelreader->val($i, 'C'),
					//'supplier_id'	=>	null,
					'item_number'	=>	$this->spreadsheetexcelreader->val($i, 'A'),
					'cost_price'	=>	$this->spreadsheetexcelreader->val($i, 'E'),
					'unit_price'	=>	$this->spreadsheetexcelreader->val($i, 'F'),
					'quantity'		=>	$this->spreadsheetexcelreader->val($i, 'I'),
					'reorder_level'	=>	$this->spreadsheetexcelreader->val($i, 'J'),
					'reorder_quantity'	=>	$this->spreadsheetexcelreader->val($i, 'K')
					);

					if($this->Item->save($item_data,$item_id)) {
						$items_taxes_data = null;
						//tax 1
						if( is_numeric($this->spreadsheetexcelreader->val($i, 'G')) ){
							$items_taxes_data[] = array('name'=>'Sales Tax 1', 'percent'=>$this->spreadsheetexcelreader->val($i, 'G') );
						}

						//taxt 2
						if( is_numeric($this->spreadsheetexcelreader->val($i, 'H')) ){
							$items_taxes_data[] = array('name'=>'Sales Tax 2', 'percent'=>$this->spreadsheetexcelreader->val($i, 'H') );
						}

						// save tax values
						if(count($items_taxes_data) > 0){
							$this->Item_taxes->save($items_taxes_data, $item_id);
						}
						$successCode[] = $item_code;
						
						//Ramel Inventory Tracking
						//update Inventory count details from Excel Import
							$item_code = $this->spreadsheetexcelreader->val($i, 'A');
							$item_id = $this->Item->get_item_id($item_code);
							$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
							$emp_info=$this->Employee->get_info($employee_id);
							$comment ='Qty Excel Imported: means BEGIN/RESET/ACTUAL count';
							$excel_data = array
								(
								'trans_items'=>$item_id,
								'trans_user'=>$employee_id,
								'trans_comment'=>$comment,
								'trans_inventory'=>$this->spreadsheetexcelreader->val($i, 'I')
								);
								$this->db->insert('inventory',$excel_data);
						//------------------------------------------------Ramel
					}
					else//insert or update item failure
					{
						$failCodes[] = $item_code ;
					}
				}

			} else {
				// rowCount < 2
				echo json_encode( array('success'=>true,'message'=>'Your upload file has no data or not in supported format.') );
				return;
			}
		}

		$success = true;
		if(count($failCodes) > 1){
			$msg = "Most items imported. But some were not, here is list of their CODE (" .count($failCodes) ."): ".implode(", ", $failCodes);
			$success = false;
		}else{
			$msg = "Import items successful";
		}

		echo json_encode( array('success'=>$success,'message'=>$msg) );
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