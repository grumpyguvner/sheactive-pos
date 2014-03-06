<?php
    $lp = fopen("/dev/lp0", 'a');

    //Reset Printer
    fwrite($lp, "\x1B\x40");
    //Bold on
    fwrite($lp, "\x1B\x45\x01");
    fwrite($lp, "\n");
    //Double width on
    fwrite($lp, "\x1B\x21\x20");
    fwrite($lp, str_pad($this->config->item('company'), 21, " ", STR_PAD_BOTH)."\n");
    //Double width off
    fwrite($lp, "\x1B\x21\x00");
    fwrite($lp, str_pad($this->config->item('address1'), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, str_pad($this->config->item('address2'), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, str_pad($this->config->item('phone'), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, "\n");
    fwrite($lp, str_pad($transaction_time, 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, "\n");

    fwrite($lp, $this->lang->line('sales_id').": ".$sale_id."\n");
    fwrite($lp, $this->lang->line('employees_employee').": ".$employee."\n");
//    fwrite($lp, $this->lang->line('customers_customer').": ".$customer."\n");
    fwrite($lp, "\n");

    foreach($cart as $line=>$item)
    {
            if($item['quantity']<0)
            {
                fwrite($lp, $this->lang->line('sales_returned_item')."\n");
            }
            fwrite($lp, str_pad(substr($item['item_number'],0,13), 14, " ", STR_PAD_RIGHT));
            fwrite($lp, str_pad(substr($item['name'],0,19), 20, " ", STR_PAD_RIGHT));
            $amount = ($item['price'])*$item['quantity'];
            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");

            fwrite($lp, str_pad(substr($item['description'],0,33), 34, " ", STR_PAD_RIGHT));
            fwrite($lp, str_pad("", 8, " ", STR_PAD_LEFT)."\n");

            if($item['discount']<>0){
                switch ($item['discount_type']){
                    case '%':
                        $myDisc = ($item['price'] * $item['discount'] / 100)*$item['quantity'];
                        fwrite($lp, str_pad(substr("DISCOUNT ".$item['discount_reason'],0,33), 34, " ", STR_PAD_LEFT));
                        $amount = ($myDisc)*-1;
                        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
                        break;
//                    case '£':
//                    case '€':
                    default:
                        $myDisc = $item['discount']*$item['quantity'];
                        fwrite($lp, str_pad(substr("DISCOUNT ".$item['discount_reason'],0,33), 34, " ", STR_PAD_LEFT));
                        $amount = ($myDisc)*-1;
                        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
                        break;
//                    default:
//                        $myDisc = 0;
                }
            }
    }

    fwrite($lp, "\n");
    fwrite($lp, str_pad(substr($this->lang->line('sales_sub_total'),0,33), 34, " ", STR_PAD_LEFT));
    $amount = $subtotal;
    fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");

    foreach($taxes as $name=>$value) {
        fwrite($lp, str_pad(substr($name,0,33), 34, " ", STR_PAD_LEFT));
        $amount = $value;
        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
    }

    fwrite($lp, str_pad(substr($this->lang->line('sales_total'),0,33), 34, " ", STR_PAD_LEFT));
    $amount = $total;
    fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
    fwrite($lp, "\n");

    $paid = 0;
    foreach($payments as $payment_id=>$payment) {
//        fwrite($lp, str_pad(substr($this->lang->line('sales_payment')." ".$payment['payment_type'],0,33), 34, " ", STR_PAD_LEFT));
        if(substr($payment['payment_type'],0,33)=="CHANGE GIVEN:")
            fwrite($lp, "\n");
        fwrite($lp, str_pad(substr($payment['payment_type'],0,33), 34, " ", STR_PAD_LEFT));
        $amount = $payment['payment_amount']*1;
        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
        $paid += $amount;
    }

    $amount = ($paid - $total) * -1;
    if($amount<>0){
        fwrite($lp, "\n");
        fwrite($lp, str_pad(substr($this->lang->line('sales_change_due'),0,33), 34, " ", STR_PAD_LEFT));
        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
    }

    fwrite($lp, "\n");
    fwrite($lp, "\n");
    fwrite($lp, str_pad(($this->config->item('vat_number')), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, "\n");
    fwrite($lp, str_pad(($this->config->item('return_policy1')), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, str_pad(($this->config->item('return_policy2')), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, str_pad(($this->config->item('return_policy3')), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, str_pad(($this->config->item('return_policy4')), 42, " ", STR_PAD_BOTH)."\n");
    fwrite($lp, "\n");
    fwrite($lp, str_pad(($this->config->item('website')), 42, " ", STR_PAD_BOTH)."\n");

    fwrite($lp, "\n");
    //Centre Align:
    fwrite($lp, "\x1B\x61\x01");
    //Barcode CODE39:
    fwrite($lp, "\x1D\x68\x28\x1D\x6B\x04".$sale_id."\x00");
    //Left Align:
    fwrite($lp, "\x1B\x61\x01");
    fwrite($lp, str_pad($sale_id, 42, " ", STR_PAD_BOTH)."\n");
    
    fwrite($lp, "\n\n\n\n\n\n");
    //Cut the paper
    fwrite($lp, "\x1D\x56\x01");


    if(isset($customer))
    {
          //Reset Printer
        fwrite($lp, "\x1B\x40");
        //Bold on
        fwrite($lp, "\x1B\x45\x01");
        fwrite($lp, "\n");
        //Double width on
        fwrite($lp, "\x1B\x21\x20");
        fwrite($lp, str_pad($this->config->item('company'), 21, " ", STR_PAD_BOTH)."\n");
        //Double width off
        fwrite($lp, "\x1B\x21\x00");
        fwrite($lp, str_pad($this->config->item('address1'), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, str_pad($this->config->item('address2'), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, str_pad($this->config->item('phone'), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, "\n");
        fwrite($lp, str_pad($transaction_time, 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, "\n");

        fwrite($lp, $this->lang->line('sales_id').": ".$sale_id."\n");
        fwrite($lp, $this->lang->line('employees_employee').": ".$employee."\n");
        fwrite($lp, $this->lang->line('customers_customer').": ".$customer."\n");
        fwrite($lp, "\n");

        foreach($cart as $line=>$item)
        {
                if($item['quantity']<0)
                {
                    fwrite($lp, $this->lang->line('sales_returned_item')."\n");
                }
                fwrite($lp, str_pad(substr($item['item_number'],0,13), 14, " ", STR_PAD_RIGHT));
                fwrite($lp, str_pad(substr($item['name'],0,19), 20, " ", STR_PAD_RIGHT));
                $amount = ($item['price'])*$item['quantity'];
                fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");

                fwrite($lp, str_pad(substr($item['description'],0,33), 34, " ", STR_PAD_RIGHT));
                fwrite($lp, str_pad("", 8, " ", STR_PAD_LEFT)."\n");

                if($item['discount']<>0){
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = ($item['price'] * $item['discount'] / 100)*$item['quantity'];
                            fwrite($lp, str_pad(substr("DISCOUNT ".$item['discount_reason'],0,33), 34, " ", STR_PAD_LEFT));
                            $amount = ($myDisc)*-1;
                            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
                            break;
                        case '£':
                        case '€':
                            $myDisc = $item['discount']*$item['quantity'];
                            fwrite($lp, str_pad(substr("DISCOUNT ".$item['discount_reason'],0,33), 34, " ", STR_PAD_LEFT));
                            $amount = ($myDisc)*-1;
                            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
                            break;
                        default:
                            $myDisc = 0;
                    }
                }
        }

        fwrite($lp, "\n");
        fwrite($lp, str_pad(substr($this->lang->line('sales_sub_total'),0,33), 34, " ", STR_PAD_LEFT));
        $amount = $subtotal;
        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");

        foreach($taxes as $name=>$value) {
            fwrite($lp, str_pad(substr($name,0,33), 34, " ", STR_PAD_LEFT));
            $amount = $value;
            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
        }

        fwrite($lp, str_pad(substr($this->lang->line('sales_total'),0,33), 34, " ", STR_PAD_LEFT));
        $amount = $total;
        fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
        fwrite($lp, "\n");

        $paid = 0;
        foreach($payments as $payment_id=>$payment) {
    //        fwrite($lp, str_pad(substr($this->lang->line('sales_payment')." ".$payment['payment_type'],0,33), 34, " ", STR_PAD_LEFT));
            if(substr($payment['payment_type'],0,33)=="CHANGE GIVEN:")
                fwrite($lp, "\n");
            fwrite($lp, str_pad(substr($payment['payment_type'],0,33), 34, " ", STR_PAD_LEFT));
            $amount = $payment['payment_amount']*1;
            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
            $paid += $amount;
        }

        $amount = ($paid - $total) * -1;
        if($amount<>0){
            fwrite($lp, "\n");
            fwrite($lp, str_pad(substr($this->lang->line('sales_change_due'),0,33), 34, " ", STR_PAD_LEFT));
            fwrite($lp, str_pad(substr(number_format($amount,2),0,8), 8, " ", STR_PAD_LEFT)."\n");
        }

        fwrite($lp, "\n");
        fwrite($lp, "\n");
        fwrite($lp, str_pad(($this->config->item('vat_number')), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, "\n");
        fwrite($lp, str_pad(($this->config->item('return_policy1')), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, str_pad(($this->config->item('return_policy2')), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, str_pad(($this->config->item('return_policy3')), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, str_pad(($this->config->item('return_policy4')), 42, " ", STR_PAD_BOTH)."\n");
        fwrite($lp, "\n");
        fwrite($lp, "\n");
        fwrite($lp, "\n");
        fwrite($lp, "\n");
        fwrite($lp, "SIGNED: _________________________________\n");
        fwrite($lp, $this->lang->line('customers_customer').": ".$customer."\n");
        fwrite($lp, $customer_address_1."\n");
        fwrite($lp, $customer_address_2."\n");
        fwrite($lp, $customer_telephone."\n");
        fwrite($lp, "\n");
        fwrite($lp, str_pad(($this->config->item('website')), 42, " ", STR_PAD_BOTH)."\n");

        fwrite($lp, "\n");
        //Centre Align:
        fwrite($lp, "\x1B\x61\x01");
        //Barcode CODE39:
        fwrite($lp, "\x1D\x68\x28\x1D\x6B\x04".$sale_id."\x00");
        //Left Align:
        fwrite($lp, "\x1B\x61\x01");
        fwrite($lp, str_pad($sale_id, 42, " ", STR_PAD_BOTH)."\n");

        fwrite($lp, "\n\n\n\n\n\n");
        //Cut the paper
        fwrite($lp, "\x1D\x56\x01");
        }

    //Kick the drawer
    fwrite($lp, "\x1B\x70\x00\x19\xF0");

    fclose($lp);
?>