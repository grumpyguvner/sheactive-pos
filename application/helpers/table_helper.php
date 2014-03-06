<?php
/*
Gets the html table to manage people.
*/
function get_people_manage_table($people,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_people_manage_table_data_rows($people,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the people.
*/
function get_people_manage_table_data_rows($people,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($people->result() as $person)
	{
		$table_data_rows.=get_person_data_row($person,$controller);
	}
	
	if($people->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='6'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_person_data_row($person,$controller)
{
	$CI =& get_instance();
	$controller_name=$CI->uri->segment(1);
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$person->person_id' value='".$person->person_id."'/></td>";
	$table_data_row.='<td width="20%">'.character_limiter($person->last_name,13).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->first_name,13).'</td>';
	$table_data_row.='<td width="30%">'.mailto($person->email,character_limiter($person->email,22)).'</td>';
	$table_data_row.='<td width="20%">'.character_limiter($person->phone_number,13).'</td>';		
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$person->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage suppliers.
*/
function get_supplier_manage_table($suppliers,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('suppliers_company_name'),
	$CI->lang->line('common_last_name'),
	$CI->lang->line('common_first_name'),
	$CI->lang->line('common_email'),
	$CI->lang->line('common_phone_number'),
	'&nbsp');
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_supplier_manage_table_data_rows($suppliers,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the supplier.
*/
function get_supplier_manage_table_data_rows($suppliers,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($suppliers->result() as $supplier)
	{
		$table_data_rows.=get_supplier_data_row($supplier,$controller);
	}
	
	if($suppliers->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='7'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_supplier_data_row($supplier,$controller)
{
	$CI =& get_instance();
	$controller_name=$CI->uri->segment(1);
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$supplier->person_id' value='".$supplier->person_id."'/></td>";
	$table_data_row.='<td width="17%">'.character_limiter($supplier->company_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->last_name,13).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->first_name,13).'</td>';
	$table_data_row.='<td width="22%">'.mailto($supplier->email,character_limiter($supplier->email,22)).'</td>';
	$table_data_row.='<td width="17%">'.character_limiter($supplier->phone_number,13).'</td>';		
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$supplier->person_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';		
	$table_data_row.='</tr>';
	
	return $table_data_row;
}

/*
Gets the html table to manage items.
*/
function get_items_manage_table($items,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';
	
	$headers = array('<input type="checkbox" id="select_all" />', 
	$CI->lang->line('items_item_number'),
	$CI->lang->line('items_name'),
	$CI->lang->line('items_category'),
	$CI->lang->line('items_cost_price'),
	$CI->lang->line('items_unit_price'),
	$CI->lang->line('items_tax_percents'),
	$CI->lang->line('items_quantity'),
	'&nbsp', 
	'Inventory'//Ramel Inventory Tracking
	);
	
	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_items_manage_table_data_rows($items,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the items.
*/
function get_items_manage_table_data_rows($items,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';
	
	foreach($items->result() as $item)
	{
		$table_data_rows.=get_item_data_row($item,$controller);
	}
	
	if($items->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='11'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('items_no_items_to_display')."</div></tr></tr>";
	}
	
	return $table_data_rows;
}

function get_item_data_row($item,$controller)
{
	$CI =& get_instance();
	$location=$CI->Location->get_info($item->location_id);
	$controller_name=$CI->uri->segment(1);
	$width = $controller->get_form_width();
        
	$table_data_row='<tr>';
	$table_data_row.="<td><input type='checkbox' id='item_$item->item_id' value='".$item->item_id."'/></td>";
	$table_data_row.='<td>'.anchor($controller_name."/view/$item->item_id/width:$width", $item->item_number, array('class'=>'thickbox','title'=>'edit item')).'</td>';
	$table_data_row.='<td>'.$item->name.'<br\>'.$item->description.'</td>';
	$table_data_row.='<td>'.($item->cost_price).'</td>';
	$table_data_row.='<td>'.($item->unit_price).'</td>';
	$table_data_row.='<td>'.$item->supplierref.'</td>';
	$table_data_row.='<td>'.anchor($controller_name."/count_details/$item->item_id/width:$width", $item->quantity, array('class'=>'thickbox','title'=>'inventory')).'</td>';
	$table_data_row.='<td>'.($item->reorder_level).'</td>';
	$table_data_row.='<td>'.($item->reorder_quantity).'</td>';
	$table_data_row.='<td>'.anchor($controller_name."/move/$item->item_id/width:$width", $location->location_ref, array('class'=>'thickbox','title'=>'move')).'</td>';
	
	$table_data_row.='</tr>';
	return $table_data_row;
}

/*
Gets the html table to manage branches.
*/
function get_branch_manage_table($branches,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('branches_branch_name'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_branch_manage_table_data_rows($branches,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the branch.
*/
function get_branch_manage_table_data_rows($branches,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($branches->result() as $branch)
	{
		$table_data_rows.=get_branch_data_row($branch,$controller);
	}

	if($branches->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='3'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('common_no_persons_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_branch_data_row($branch,$controller)
{
	$CI =& get_instance();
	$controller_name=$CI->uri->segment(1);
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='person_$branch->branch_ref' value='".$branch->branch_ref."'/></td>";
	$table_data_row.='<td width="73%">'.character_limiter($branch->branch_name,13).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$branch->branch_ref/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';

	return $table_data_row;
}


/*
Gets the html table to manage locations.
*/
function get_location_manage_table($locations,$controller)
{
	$CI =& get_instance();
	$table='<table class="tablesorter" id="sortable_table">';

	$headers = array('<input type="checkbox" id="select_all" />',
	$CI->lang->line('locations_location_ref'),
	$CI->lang->line('locations_location_comment'),
	'&nbsp');

	$table.='<thead><tr>';
	foreach($headers as $header)
	{
		$table.="<th>$header</th>";
	}
	$table.='</tr></thead><tbody>';
	$table.=get_location_manage_table_data_rows($locations,$controller);
	$table.='</tbody></table>';
	return $table;
}

/*
Gets the html data rows for the location.
*/
function get_location_manage_table_data_rows($locations,$controller)
{
	$CI =& get_instance();
	$table_data_rows='';

	foreach($locations->result() as $location)
	{
		$table_data_rows.=get_location_data_row($location,$controller);
	}

	if($locations->num_rows()==0)
	{
		$table_data_rows.="<tr><td colspan='3'><div class='warning_message' style='padding:7px;'>".$CI->lang->line('locations_no_locations_to_display')."</div></tr></tr>";
	}

	return $table_data_rows;
}

function get_location_data_row($location,$controller)
{
	$CI =& get_instance();
	$controller_name=$CI->uri->segment(1);
	$width = $controller->get_form_width();

	$table_data_row='<tr>';
	$table_data_row.="<td width='5%'><input type='checkbox' id='location_$location->location_id' value='".$location->location_id."'/></td>";
	$table_data_row.='<td width="17%">'.character_limiter($location->location_ref,20).'</td>';
	$table_data_row.='<td width="73%">'.character_limiter($location->location_comment,50).'</td>';
	$table_data_row.='<td width="5%">'.anchor($controller_name."/view/$location->location_id/width:$width", $CI->lang->line('common_edit'),array('class'=>'thickbox','title'=>$CI->lang->line($controller_name.'_update'))).'</td>';
	$table_data_row.='</tr>';

	return $table_data_row;
}

?>