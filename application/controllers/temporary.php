<?php

	//This method should import new and updated records
	//from the activewms database
	function importOrders($startref="",$limit=""){
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

		$post_data["data"]="DISTINCT    orders.orderdate  AS orderdate,
                                                leadsource        AS channel,
                                                webconfirmationno AS reference,
                                                billtoemail       AS email,
                                                billtoname        AS billingname,
                                                billtoaddress1    AS billingaddress1,
                                                billtoaddress2    AS billingaddress2,
                                                billtocity        AS billingcity,
                                                billtostate       AS billingstate,
                                                billtopostcode    AS billingpostcode,
                                                billtocountry     AS billingcountry,
                                                shiptoname        AS shippingname,
                                                shiptoaddress1    AS shippingaddress1,
                                                shiptoaddress2    AS shippingaddress2,
                                                shiptocity        AS shippingcity,
                                                shiptostate       AS shippingstate,
                                                shiptopostcode    AS shippingpostcode,
                                                shiptocountry     AS shippingcountry,
                                                shippingmethod    AS shippingmethod
                                         FROM orders
                                         WHERE ISNULL(orders.statusid)";

//			if(!$startref==""){
//                            $post_data["data"].=" WHERE NOT (CONCAT('2', products.bleepid) < ".$startref.")";
//                            $post_data["data"].=" ORDER BY (CONCAT('2', products.bleepid))";
//                        }else{
//                            $post_data["data"].=" WHERE ('".$lastupdate."' < products.modifieddate OR '".$lastupdate."' < styles.modifieddate)";
//                            $post_data["data"].=" ORDER BY (styles.modifieddate)";
//                        }

//		if(!$limit=="") $post_data["data"].=" LIMIT ".$limit;
//                log_message('debug',$post_data["data"]);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://'.$this->config->item('ACTIVEWMS_ADDR').'/modules/activewms/ediquery.php');
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$activewmsOrders = curl_exec($curl);
		if (curl_errno($curl)) {
                        log_message('error',curl_error($curl));
			return false;
		} else {
			curl_close($curl);
		}

		$activewmsOrders = explode(chr(10),$activewmsOrders);
		$recordsProcessed = 0;

		// We retreive all product IN ACTIVEWMS
		foreach ($activewmsOrders as $activewmsOrder){

log_message('debug',"record ".$recordsProcessed);
			$lineOrder = explode(chr(9),$activewmsOrder);
			if (is_array($lineOrder) && isset($lineOrder[0]) && !empty($lineOrder[0])){
				$orderdate = $lineOrder[0];
				$channel = $lineOrder[1];
				$reference = $lineOrder[2];
				$email = $lineOrder[3];
				$billingname = $lineOrder[4];
				$billingaddress1 = $lineOrder[5];
				$billingaddress2 = $lineOrder[6];
				$billingcity = $lineOrder[7];
				$billingstate = $lineOrder[8];
				$billingpostcode = $lineOrder[9];
				$billingcountry = $lineOrder[10];
				$shippingname = $lineOrder[11];
				$shippingaddress1 = $lineOrder[12];
				$shippingaddress2 = $lineOrder[13];
				$shippingcity = $lineOrder[14];
				$shippingstate = $lineOrder[15];
				$shippingpostcode = $lineOrder[16];
				$shippingcountry = $lineOrder[17];
				$shippingmethod = $lineOrder[18];

                                log_message('debug',"************ BEGINING NEXT ORDER ************");
                                log_message('debug','order --> '.$reference.' name --> '.$name.' price --> '.$price.' promo price --> '.$reduction_price.' promo percent --> '.$reduction_percent.' wholesale --> '.$wholesale_price);

				//create new customer if it doesn't exist
				$id_customer = $this->Customer->get_id_by_email($email);

                                if(!$id_customer){

                                    $person_data = array(
                                    'first_name'=>'',
                                    'last_name'=>$billingname,
                                    'email'=>$email,
                                    'phone_number'=>'',
                                    'address_1'=>$billingaddress1,
                                    'address_2'=>$billingaddress2,
                                    'city'=>$billingcity,
                                    'state'=>$billingstate,
                                    'zip'=>$billingpostcode,
                                    'country'=>$billingcountry,
                                    'comments'=>'imported from headoffice.',
                                    );
                                    $customer_data=array(
                                    'company_name'=>$supplier,
                                    'account_number'=>$supplier,
                                    );
                                    if(!$this->Supplier->save($person_data,$customer_data,$id_customer))
                                    {
                                        log_message('error',"Unable to create new supplier [".$supplier."]");
                                        return false;
                                    }
                                }
				$id_customer = $this->Supplier->get_id_by_companyname($supplier);

				//find existing id
				$item_id = $this->Item->get_id_by_plu($reference);
                                $item_data = array(
                                'name'=>$name,
                                'description'=>$description,
                                'category'=>$category,
                                'supplier_id'=>$id_customer,
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
?>
