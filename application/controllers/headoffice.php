<?php
set_time_limit(0);

//require_once ("secure_area.php");
//class Headoffice extends Secure_area
class Headoffice extends Controller
{
	function __construct()
	{
            log_message('DEBUG', 'Headoffice Controller: Construct Begin');
//TEMP login TO ALLOW AUTO HO UPDATES
//$this->load->model('Employee');
//if(!$this->Employee->is_logged_in())
//    $this->Employee->login('itadmin','gated98!polo');
		parent::__construct('headoffice');
//            log_message('DEBUG', 'Headoffice Controller: Construct End');
	}
	
	function index()
	{
//            log_message('DEBUG', 'Headoffice Controller: Index Begin');
		$this->load->view("headoffice");
//            log_message('DEBUG', 'Headoffice Controller: Index End');
	}
		
	function save()
	{
		$batch_save_data=array(
		'ho_company'=>$this->input->post('company'),
		'ho_address'=>$this->input->post('address'),
		'ho_phone'=>$this->input->post('phone'),
		'ho_email'=>$this->input->post('email'),
		'ho_fax'=>$this->input->post('fax'),
		'ho_website'=>$this->input->post('website'),
		'ho_branch'=>$this->input->post('branch'),
		'ho_default_tax_1_rate'=>$this->input->post('default_tax_1_rate'),
		'ho_default_tax_1_name'=>$this->input->post('default_tax_1_name'),
		'ho_default_tax_2_rate'=>$this->input->post('default_tax_2_rate'),
		'ho_default_tax_2_name'=>$this->input->post('default_tax_2_name'),
		'ho_language'=>$this->input->post('language'),
		'ho_timezone'=>$this->input->post('timezone')
		);
		
		if($this->HOconfig->batch_save($batch_save_data))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('config_saved_successfully')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('config_saved_unsuccessfully')));
	
		}
	}

	//This method should import new and updated records
	//from the activewms database
	function importProducts($startref="",$limit=""){
log_message("DEBUG","start=".$startref.",limit=".$limit);
		// fetch all products from activeWMS
		$post_data["phpbmsusername"]=$this->config->item('ACTIVEWMS_USER');
		$post_data["phpbmspassword"]=$this->config->item('ACTIVEWMS_PASS');
		$lastupdate=$this->config->item('ACTIVEWMS_LASTUPDATE');
		if(($limit=="" || $limit=="0") && ($startref=="" || $startref=="0")){
			$startref=$this->config->item('ACTIVEWMS_LASTREF');
                	$limit=$this->config->item('ACTIVEWMS_LIMIT');
		}
log_message("DEBUG","start=".$startref.",limit=".$limit);

		$post_data["data"]="DISTINCT(CONCAT('2', products.bleepid)) AS reference,
                                                IFNULL((SELECT stylename FROM styles_translations WHERE (styles_translations.styleid = styles.uuid AND styles_translations.site = '".$this->config->item('ho_branch')."' AND styles_translations.inactive = 0)),
                                                    styles.stylename
                                                ) AS stylename,
                                                CONCAT(
                                                    IFNULL((SELECT name FROM sizes_translations WHERE (sizes_translations.sizeid = products.sizeid AND sizes_translations.site = '".$this->config->item('ho_branch')."' AND sizes_translations.inactive = 0)),
                                                        (SELECT name FROM sizes WHERE (sizes.uuid = products.sizeid))
                                                    ),', ',
                                                    IFNULL((SELECT name FROM colours_translations WHERE (colours_translations.colourid = products.colourid AND colours_translations.site = '".$this->config->item('ho_branch')."' AND colours_translations.inactive = 0)),
                                                        (SELECT name FROM colours WHERE (colours.uuid = products.colourid))
                                                    )
                                                ) AS description,
                                                IFNULL((SELECT name FROM producttypes_translations WHERE (producttypes_translations.producttypeid = styles.producttypeid AND producttypes_translations.site = '".$this->config->item('ho_branch')."' AND producttypes_translations.inactive = 0)),
                                                    (SELECT name FROM producttypes WHERE (producttypes.uuid = styles.producttypeid))
                                                ) AS category,
                                                IFNULL((SELECT name FROM suppliers_translations WHERE (suppliers_translations.supplierid = styles.supplierid AND suppliers_translations.site = '".$this->config->item('ho_branch')."' AND suppliers_translations.inactive = 0)),
                                                    (SELECT name FROM suppliers WHERE (suppliers.uuid = styles.supplierid))
                                                ) AS supplier,
                                                IFNULL((SELECT price FROM styles_translations WHERE (styles_translations.styleid = styles.uuid AND styles_translations.site = '".$this->config->item('ho_branch')."' AND styles_translations.inactive = 0)),
                                                    styles.unitprice
                                                ) AS price,
                                                IFNULL((SELECT reduction_price FROM styles_translations WHERE (styles_translations.styleid = styles.uuid AND styles_translations.site = '".$this->config->item('ho_branch')."' AND styles_translations.inactive = 0)),
                                                    styles.saleprice
                                                ) AS reduction_price,
                                                IFNULL((SELECT reduction_percent FROM styles_translations WHERE (styles_translations.styleid = styles.uuid AND styles_translations.site = '".$this->config->item('ho_branch')."' AND styles_translations.inactive = 0)),
                                                    0
                                                ) AS reduction_percent,
                                                IFNULL((SELECT wholesale_price FROM styles_translations WHERE (styles_translations.styleid = styles.uuid AND styles_translations.site = '".$this->config->item('ho_branch')."' AND styles_translations.inactive = 0)),
                                                    styles.unitcost
                                                ) AS wholesale_price,
                                                products.supplierref,
                                                products.modifieddate AS modifieddate
                                         FROM products
                                    LEFT JOIN styles
                                              ON (products.styleid = styles.uuid)";

			if(!$startref==""){
                            $post_data["data"].=" WHERE NOT (CONCAT('2', products.bleepid) < ".$startref.")";
                            $post_data["data"].=" ORDER BY (CONCAT('2', products.bleepid))";
                        }else{
                            $post_data["data"].=" WHERE ('".$lastupdate."' < products.modifieddate OR '".$lastupdate."' < styles.modifieddate)";
                            $post_data["data"].=" ORDER BY (styles.modifieddate)";
                        }

		if(!$limit=="") $post_data["data"].=" LIMIT ".$limit;
                log_message('debug',$post_data["data"]);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$this->config->item('ACTIVEWMS_ADDR').'/modules/activewms/ediquery.php');
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$activewmsProducts = curl_exec($curl);
		if (curl_errno($curl)) {
                        log_message('error',curl_error($curl));
			return false;
		} else {
			curl_close($curl);
		}

		$activewmsProducts = explode(chr(10),$activewmsProducts);
		$recordsProcessed = 0;

		// We retreive all product IN ACTIVEWMS
		foreach ($activewmsProducts as $activewmsProduct){

log_message('debug',"record ".$recordsProcessed);
			$lineProduct = explode(chr(9),$activewmsProduct);
			if (is_array($lineProduct) && isset($lineProduct[0]) && !empty($lineProduct[0])){
				$reference = $lineProduct[0];
				$name = $lineProduct[1];
				$description = $lineProduct[2];
				$category = strtoupper($lineProduct[3]);
				$supplier = strtoupper($lineProduct[4]);
				$price = $lineProduct[5];
				$reduction_price = $lineProduct[6];
				$reduction_percent = $lineProduct[7];
				$wholesale_price = $lineProduct[8];
				$supplierref = $lineProduct[9];
				$modifieddate = $lineProduct[10];

                                $sellingprice = $price;
                                if($reduction_price>0){
                                    $sellingprice = $reduction_price;
                                }
                                elseif($reduction_percent>0){
                                    $sellingprice = $price - ($price * ($reduction_percent/100));
                                }
                                log_message('debug',"************ BEGINING NEXT STYLE ************");
                                log_message('debug','style --> '.$reference.' name --> '.$name.' price --> '.$price.' promo price --> '.$reduction_price.' promo percent --> '.$reduction_percent.' wholesale --> '.$wholesale_price);

				//create new supplier if it doesn't exist
				$id_supplier = $this->Supplier->get_id_by_companyname($supplier);
                                if(!$id_supplier){

                                    $person_data = array(
                                    'first_name'=>'',
                                    'last_name'=>$supplier,
                                    'email'=>'',
                                    'phone_number'=>'',
                                    'address_1'=>'',
                                    'address_2'=>'',
                                    'city'=>'',
                                    'state'=>'',
                                    'zip'=>'',
                                    'country'=>'',
                                    'comments'=>'imported from headoffice.',
                                    );
                                    $supplier_data=array(
                                    'company_name'=>$supplier,
                                    'account_number'=>$supplier,
                                    );
                                    if(!$this->Supplier->save($person_data,$supplier_data,$id_supplier))
                                    {
                                        log_message('error',"Unable to create new supplier [".$supplier."]");
                                        return false;
                                    }
                                }
				$id_supplier = $this->Supplier->get_id_by_companyname($supplier);

				//find existing id
				$item_id = $this->Item->get_id_by_plu($reference);
                                $item_data = array(
                                'name'=>$name,
                                'description'=>$description,
                                'category'=>$category,
                                'supplier_id'=>$id_supplier,
                                'supplierref'=>$supplierref,
                                'item_number'=>$reference,
                                'cost_price'=>$wholesale_price,
                                'unit_price'=>$sellingprice,
                                'retail_price'=>$price
                                );

//                                $employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
                                $employee_id=1;
                                $cur_item_info = $this->Item->get_info($item_id);

                                if(!$this->Item->save($item_data,$item_id)){
                                    log_message('error',"Unable to save product data for [".$reference."]");
                                    return false;
                                }

				$recordsProcessed=$recordsProcessed+1;

			}//endif lineproduct

                        $this->HOconfig->save('ACTIVEWMS_LASTREF', $reference);

		}//end foreach

		echo $recordsProcessed." product records processed.\n";
		if($recordsProcessed > 0 && ($limit!="" || $limit=="0")){
                    // WE CAN ONLY SET LAST UPDATED IF WE DIDNT LIMIT THE SELECTION!
                    if ($modifieddate > $lastupdate)
                        $this->HOconfig->save('ACTIVEWMS_LASTUPDATE', $modifieddate);
		}
                if($recordsProcessed < 1){
                    //reset count to begining
                    $this->HOconfig->save('ACTIVEWMS_LASTREF', "0");
                }


	}//end method --importProducts--

	//This method should get transfers for specified Branch
	function getTransfers($branch, $id){
            $this->load->model('headoffice_exports/detailed_transfers');
            $model = $this->detailed_transfers;
            $datesent = date('Y-m-d H:m:s');
            $tabular_data = array();
            //fetch rows for specified branch & id
            $export_data = $model->getData(array('branch_ref'=>$branch,'transfer_id'=>$id));

            foreach($export_data as $row)
            {
		echo $row['transfer_id'].chr(9);
		echo $row['transfer_time'].chr(9);
		echo $row['comment'].chr(9);
		echo $row['item_number'].chr(9);
		echo $row['ean_upc'].chr(9);
		echo $row['name'].chr(9);
		echo $row['description'].chr(9);
		echo $row['quantity_transfered'].chr(9);
		echo $row['cost_price'].chr(9);
		echo $row['unit_price'].chr(9);
		echo $row['company_name'].chr(9);
		echo $row['supplierref'].chr(9);
		echo chr(10);
            }

	}//end method --exportStock--

	//This method should import new and updated records
	//from the billingshurst stock system
	function importTransfers(){
echo "debug 1";
		// fetch all transfers from activePOS
//		$post_data["phpbmsusername"]=$this->config->item('ACTIVEWMS_USER');
//		$post_data["phpbmspassword"]=$this->config->item('ACTIVEWMS_PASS');
                $idSetting = $this->config->item('ho_branch');
                $idSetting = $idSetting.'_LASTTRANSFER';
                $nextID = $this->config->item($idSetting);
                if($nextID=="") {
                    $this->HOconfig->save($idSetting, "1");
                    $nextID = "1";
                }

echo "debug 2";
		$curl = curl_init();
//		curl_setopt($curl, CURLOPT_URL, 'http://'.$this->config->item('ACTIVEWMS_ADDR').'/modules/activewms/ediquery.php');
//		//
                // TODO: should circle round multiple branches fetching transfers from all ..
                // hold URL against BRANCH when this is done cant use same transfer id ???
                //
                $from_branch = "WEBSTORE";
                $post_data = array();
		curl_setopt($curl, CURLOPT_URL, 'http://billingshurst.sheactive.net/fetch_transfers/'.$this->config->item('ho_branch').'/'.$nextID);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
echo "debug 3";

		$activewmsTransfers = curl_exec($curl);
		if (curl_errno($curl)) {
                        log_message('error',curl_error($curl));
			return false;
		} else {
			curl_close($curl);
		}

echo "debug 4<br/>";
echo "before=".$activewmsTransfers."<br/>";

		$activewmsTransfers = explode(chr(10),$activewmsTransfers);
echo "after=".$activewmsTransfers."<br/>";
		$recordsProcessed = 0;

echo "debug 5";
		// We retreive all product IN ACTIVEWMS
		foreach ($activewmsTransfers as $activewmsTransfer){

log_message('debug',"record ".$recordsProcessed);
			$lineTransfer = explode(chr(9),$activewmsTransfer);
			if (is_array($lineTransfer) && isset($lineTransfer[0]) && !empty($lineTransfer[0])){
				$transfer_id = $lineTransfer[0];
				$transfer_time = $lineTransfer[1];
				$transfer_comment = $lineTransfer[2];
				$item_plu = $lineTransfer[3];
				$item_ean = $lineTransfer[4];
				$item_name = $lineTransfer[5];
				$item_description = $lineTransfer[6];
				$transfer_quantity = $lineTransfer[7];
				$item_cost_price = $lineTransfer[8];
				$item_unit_price = $lineTransfer[9];
				$supplier = $lineTransfer[10];
				$item_supplier_ref = $lineTransfer[11];

                                log_message('debug',"************ BEGINING NEXT ITEM ************");
                                log_message('debug','plu --> '.$item_plu.' transfer_id --> '.$transfer_id.' quantity --> '.$transfer_quantity);

		echo $transfer_id.' ¦ ';
		echo $transfer_time.' ¦ ';
		echo $transfer_comment.' ¦ ';
		echo $item_plu.' ¦ ';
		echo $item_ean.' ¦ ';
		echo $item_name.' ¦ ';
		echo $item_description.' ¦ ';
		echo $transfer_quantity.' ¦ ';
		echo $item_cost_price.' ¦ ';
		echo $item_unit_price.' ¦ ';
		echo $supplier.' ¦ ';
		echo $item_supplier_ref.' ¦ ';
		echo '<br />';

				$recordsProcessed=$recordsProcessed+1;

			}//endif lineproduct

                        $this->config->save($this->config->item('ho_branch').'_LASTTRANSFER', $transfer_id+1);

		}//end foreach

		echo $recordsProcessed." transfer records processed.\n";


	}//end method --importTransfers--

	//This method should export inventory updates
	//to the activewms database
	function exportStock(){
            $this->load->model('headoffice_exports/detailed_inventory');
            $model = $this->detailed_inventory;
            $datesent = date('Y-m-d H:m:s');
            $tabular_data = array();
            //fetch rows that haven't been sent to headoffice
            $export_data = $model->getData(array('ho_update'=>'0000-00-00 00:00:00'));

            foreach($export_data as $row)
            {
                //Export to headoffice
		$post_stock = array();
		$post_stock["TX"] = "HEADER";
                $post_stock["location"] = $this->config->item('ho_branch');
                //Take the leading "2" off
                $post_stock["plu"] = substr($row['PLU'],1);
                $post_stock["quantity"] = $row['quantity'];
                $post_stock["quantity_remaining"] = $row['items_remaining'];
                $post_stock["basket"] = $row['location'];
                $post_stock["modifieddate"] = $row['trans_date'];
                $return = $this->__postData($post_stock,"api_stockupdate");
                if ($return){
                    //if successful update record
                    $inventory_data = array
                    (
                            'ho_update'=>$datesent,
                            'ho_error'=>false
                    );
                    //add row to report
                    $tabular_data[] = array($row['trans_date'], $row['PLU'], $row['quantity']);
                }else{
                    $inventory_data = array
                    (
                            'ho_update'=>$datesent,
                            'ho_error'=>true
                    );
                }
                $this->db->update('inventory',$inventory_data,array('trans_id'=>$row['trans_id']));

            }

            $data = array(
                "title" => $this->lang->line('headoffice_stock_export_report'),
                "subtitle" => $datesent,
                "headers" => $model->getDataColumns(),
                "data" => $tabular_data,
                "summary_data" => $model->getSummaryData(array('ho_update'=>$datesent)),
                "export_excel" => 0
            );

//            $this->load->view("reports/tabular",$data);

	}//end method --exportStock--

	//This method should retry exporting inventory updates
	//to the activewms database which previously had errors
        //TODO: Add notification for subsquent failures
	function retryExportStock(){
            $this->load->model('headoffice_exports/detailed_inventory');
            $model = $this->detailed_inventory;
            $datesent = date('Y-m-d H:m:s');
            $tabular_data = array();
            //fetch rows that haven't already been sent to headoffice
            $export_data = $model->getTxErrors(array());

            foreach($export_data as $row)
            {
                //Export to headoffice
		$post_stock = array();
		$post_stock["TX"] = "HEADER";
                $post_stock["location"] = $this->config->item('ho_branch');
                //Take the leading "2" off
                $post_stock["plu"] = substr($row['PLU'],1);
                $post_stock["quantity"] = $row['quantity'];
                $post_stock["quantity_remaining"] = $row['items_remaining'];
                $post_stock["modifieddate"] = $row['trans_date'];
                $return = $this->__postData($post_stock,"api_stockupdate");
                if ($return){
                    //if successful update record
                    $inventory_data = array
                    (
                            'ho_update'=>$datesent,
                            'ho_error'=>false
                    );
                    //add row to report
                    $tabular_data[] = array($row['trans_date'], $row['PLU'], $row['quantity']);
                }else{
                    $inventory_data = array
                    (
                            'ho_update'=>$datesent,
                            'ho_error'=>$row['ho_error']+1
                    );
                }
                $this->db->update('inventory',$inventory_data,array('trans_id'=>$row['trans_id']));

            }

            $data = array(
                "title" => $this->lang->line('headoffice_stock_export_report'),
                "subtitle" => $datesent,
                "headers" => $model->getDataColumns(),
                "data" => $tabular_data,
                "summary_data" => $model->getSummaryData(array('ho_update'=>$datesent)),
                "export_excel" => 0
            );

//            $this->load->view("reports/tabular",$data);

	}//end method --retryExportStock--

	//This method should export sales & returns records
	//to the activewms database
	function exportSales(){
            $this->load->model('headoffice_exports/detailed_sales');
            $model = $this->detailed_sales;
            $datesent = date('Y-m-d H:m:s');
            $tabular_data = array();
            //fetch rows that haven't been sent to headoffice
            $export_data = $model->getData(array('ho_update'=>'0000-00-00 00:00:00'));

            foreach($export_data as $row)
            {
                //Export to headoffice
		$post_sale = array();
		$post_sale["TX"] = "DETAIL";
                $post_sale["sale_id"] = $row['sale_id'];
                $post_sale["sale_date"] = $row['sale_date'];
                $post_sale["upc"] = $row['PLU'];
                $post_sale["quantity"] = $row['items_purchased'];
                $post_sale["quantity_remaining"] = $row['items_remaining'];
                $return = $this->__postData($post_sale);
                if ($return){
                    //if successful update record
                    $sales_items_data = array
                    (
                            'ho_update'=>$datesent
                    );
                    $sales_items_where = array
                    (
                            'sale_id'=>$row['sale_id'],
                            'item_id'=>$row['item_id'],
                            'line'=>$row['line']
                    );

                    $this->db->update('sales_items',$sales_items_data,$sales_items_where);
                    //if successful add row to report
                    $tabular_data[] = array($row['sale_date'], $row['PLU'], $row['quantity_purchased'], $row['items_remaining']);
                }else{
//echo 'Problem posting cart detail line'.$this->line.'.<br/>';
                }

            }

            $data = array(
                "title" => $this->lang->line('headoffice_sales_export_report'),
                "subtitle" => $datesent,
                "headers" => $model->getDataColumns(),
                "data" => $tabular_data,
                "summary_data" => $model->getSummaryData(array('ho_update'=>$datesent)),
                "export_excel" => 0
            );

            $this->load->view("reports/tabular",$data);

	}//end method --exportSales--

        //Used for sending data
        function __postData($postData, $apiUrl = "api_activepos"){

            //add the login details if necessary
            if (!isset($postData["activewmsuser"])){
                $postData["phpbmsusername"] = $this->config->item('ACTIVEWMS_USER');
                $postData["phpbmspassword"] = $this->config->item('ACTIVEWMS_PASS');
            }
//log_message('debug',$postData);

            $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, 'http://'.$this->config->item('ACTIVEWMS_ADDR').'/modules/activewms/'.$apiUrl.'.php');
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($curl);
            if (curl_errno($curl)) {
                    log_message('error',curl_error($curl));
                    return false;
            } else {
                    curl_close($curl);
            }

            if (substr($result,0,2)=='OK'){
                    //If export was ok then we should have the uuid from activewms
                    return substr($result,2);
            }

            log_message('error',"Unable to post data:".$result);
            return false;
	}//end method --postData--

}
?>
