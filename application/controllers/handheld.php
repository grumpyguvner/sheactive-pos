<?php
set_time_limit(0);

require_once ("secure_area.php");
class handheld extends Secure_area
{
	var $CI;

  	function __construct()
	{
                parent::__construct();
		$this->CI =& get_instance();
	}

	function index()
	{
            $this->CI->session->set_userdata('handheld_file_path', "");
            $this->CI->session->set_userdata('handheld', "");
            $data = array('error' => ' ');
            $this->load->view('handheld/index', $data);
	}

	function do_upload()
	{
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'csv';
            $config['max_size']	= '1024';

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload())
            {
                    $data = array('error' => $this->upload->display_errors());

                    $this->load->view('handheld/index', $data);
            }
            else
            {

                    $upload_data = $this->upload->data();

                    $this->CI->session->set_userdata('handheld_file_path', $upload_data['full_path']);
                    $this->CI->session->set_userdata('handheld', substr($upload_data['file_name'],0,4));

                    $data = array('upload_data' => $upload_data);

                    $this->load->view('handheld/processing', $data);
            }
	}

	function process()
	{
            $this->load->helper('cookie');
            $this->load->library('csvreader');

            $filePath = $this->CI->session->userdata('handheld_file_path');
            $device = $this->CI->session->userdata('handheld');

            $csvData = $this->csvreader->parse_file($filePath);
            if (strpos($filePath,"STOCK") === false)
            {
                $results = $this->import_receives_records($device,$csvData);
                $data['Results'] = $results;
                $this->load->view('handheld/receives_results', $data);
            }else{
                $results = $this->import_stock_records($device,$csvData);
                $data['Results'] = $results;
                $this->load->view('handheld/stock_results', $data);
            }
	}

	function import_receives_records($device,$csvData)
	{
            $myloc = 0;
            $cnt = 0;

            foreach($csvData as $field){
                $line = array();

                $line['location_id'] = $field['Location'];
                if($myloc!=$line['location_id']){
                    $myloc=$line['location_id'];
                    $cnt = 1;
                }else{
                    $cnt = $cnt+1;
                }

                $line['processed'] = 0;
                $line['comment'] = "NOT PROCESSED";

//                $line['device'] = $field['Device'];
                $line['device'] = $device."-".$myloc."-".$cnt;
                switch ($field['Branch']) {
                    case 1:
                        $line['branch_ref'] = "WEBSTORE";
                        break;
                    case 2:
                        $line['branch_ref'] = "BRIGHTON";
                        break;
                }
                switch ($field['From']) {
                    case 1:
                        $line['from_branch'] = "WEBSTORE";
                        break;
                    case 2:
                        $line['from_branch'] = "BRIGHTON";
                        break;
                }

                $line['item_number'] = substr($field['PLU'],0,12);
                $line['timestamp'] = $field['Timestamp'];

                //Inventory Count Details
                $hhreceives_id = $this->CI->HHReceives->exists($line['device'],$line['item_number'],$line['timestamp']);
//echo "PLU [".$line['PLU']."]=".$line['item_number']."<br/>";
		if(!$this->CI->HHReceives->processed($hhreceives_id))
		{
                    $line['comment'] = $this->process_receives_record($line);
                    $line['processed'] = ($line['comment'] == "OK");
                }else{
                    $line['comment'] = "Record already imported and processed!";
                    $line['processed'] = 1;
                }

                $this->CI->HHReceives->save($line,$hhreceives_id);
                $results[] = $line;
            }

            return $results;
	}

	function process_receives_record($line)
	{
            $item_id = $this->CI->Item->get_id_by_plu($line['item_number']);
            //make sure item exists
            if (!$item_id){
                return "ERROR: PLU [".$line['item_number']."] not found!";
            }
            $cur_item_info = $this->Item->get_info($item_id);

            $employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;

            $received = 1;
            $remarks ='Received 1 item from '.$line['from_branch'].' by '.$line['device'].' at '.$line['location_id'];
            $inv_data = array
            (
                    'trans_date'=>$line['timestamp'],
                    'trans_items'=>$item_id,
                    'trans_user'=>$employee_id,
                    'trans_comment'=>$remarks,
                    'trans_inventory'=>$received
            );
            $this->CI->Inventory->insert($inv_data);

            //Update stock quantity
            $item_data = array('quantity'=>$cur_item_info->quantity + 1);
            $this->Item->save($item_data,$item_id);

            return "OK";
	}

	function import_stock_records($device,$csvData)
	{
            $myloc = 0;
            $cnt = 0;
            foreach($csvData as $field){
                $line = array();
                $line['location_id'] = $field['Location'];
                if($myloc!=$line['location_id']){
                    $myloc=$line['location_id'];
                    $cnt = 1;
                }else{
                    $cnt = $cnt+1;
                }

                $line['processed'] = 0;
                $line['comment'] = "NOT PROCESSED";

//                $line['device'] = $field['Device'];
                $line['device'] = $device."-".$myloc."-".$cnt;
                switch ($field['Branch']) {
                    case 1:
                        $line['branch_ref'] = "WEBSTORE";
                        break;
                    case 2:
                        $line['branch_ref'] = "BRIGHTON";
                        break;
                }

                $line['location_id'] = $field['Location'];
                $line['item_number'] = substr($field['PLU'],0,12);
                $line['timestamp'] = $field['Timestamp'];

                //Inventory Count Details
                $hhstock_id = $this->CI->HHStock->exists($line['device'],$line['item_number'],$line['timestamp']);
//echo "PLU [".$line['PLU']."]=".$line['item_number']."<br/>";
		if(!$this->CI->HHStock->processed($hhstock_id))
		{
                    $line['comment'] = $this->process_stock_record($line);
                    $line['processed'] = ($line['comment'] == "OK");
                }else{
                    $line['comment'] = "Record already imported and processed!";
                    $line['processed'] = 1;
                }

                $this->CI->HHStock->save($line,$hhstock_id);
                $results[] = $line;
            }

            return $results;
	}

	function process_stock_record($line)
	{
            $item_id = $this->CI->Item->get_id_by_plu($line['item_number']);
            //make sure item exists
            if (!$item_id){
                return "ERROR: PLU [".$line['item_number']."] not found!";
            }
            $cur_item_info = $this->Item->get_info($item_id);
            if(!$this->Location->exists($line['location_id']))
            {
                $comment = "Created during stock take ".date("d/m/Y");
                $loc_data = array
                (
                    'location_id'=>$line['location_id'],
                    'location_ref'=>$line['location_id'],
                    'location_comment'=>$comment
                );
                $this->Location->save($loc_data,$line['location_id']);
            }
            $location_ref = $this->Location->get_info($line['location_id'])->location_ref;

            $employee_id=$this->CI->Employee->get_logged_in_employee_info()->person_id;

            $found = 1;
            $remarks ='Found 1 item by '.$line['device'].' at '.$location_ref;
            $inv_data = array
            (
                    'trans_date'=>$line['timestamp'],
                    'trans_items'=>$item_id,
                    'trans_user'=>$employee_id,
                    'trans_comment'=>$remarks,
                    'trans_inventory'=>$found
            );
            $this->CI->Inventory->insert($inv_data);

            //Update stock quantity
            $item_data = array('quantity'=>$cur_item_info->quantity + 1,'location_id'=>$line['location_id']);
            $this->Item->save($item_data,$item_id);

            return "OK";
	}
}
?>
