<?php $this->load->view("partial/header"); ?>
<?php
if (isset($error_message))
{
	echo '<h1 style="text-align: center;">'.$error_message.'</h1>';
	exit;
}
?>
<div id="receipt_wrapper">
	<div id="receipt_header">
		<div id="company_name"><?php echo $this->config->item('company'); ?></div>
		<div id="company_address"><?php echo nl2br($this->config->item('address1')); ?></div>
		<div id="company_address"><?php echo nl2br($this->config->item('address2')); ?></div>
		<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
		<div id="sale_receipt"><?php echo $receipt_title; ?></div>
		<div id="sale_time"><?php echo $transaction_time ?></div>
	</div>
	<div id="receipt_general_info">
		<?php if(isset($customer))
		{
		?>
			<div id="customer"><?php echo $this->lang->line('customers_customer').": ".$customer; ?></div>
		<?php
		}
		?>
		<div id="sale_id"><?php echo $this->lang->line('sales_id').": ".$sale_id; ?></div>
		<div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></div>
	</div>

	<table id="receipt_items">
	<tr>
	<th style="width:30%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
	<th style="width:40%;"><?php echo $this->lang->line('items_item'); ?></th>
	<th style="width:15%;text-align:right;">&nbsp;</th>
	<th style="width:15%;text-align:right;"><?php echo $this->lang->line('sales_total'); ?></th>
	</tr>
	<?php
	foreach($cart as $line=>$item)
	{
        	if($item['quantity']<0)
        	{
        		echo "<tr><td colspan=4>".$this->lang->line('sales_returned_item')."</td></tr>";
        	}
	?>
		<tr>
		<td><?php echo $item['item_number']; ?></td>
		<td><span class='long_name'><?php echo $item['name']; ?></span><span class='short_name'><?php echo character_limiter($item['name'],10); ?></span></td>
		<td colspan="2" style='text-align:right;'><?php echo to_currency(($item['price'])*$item['quantity']); ?></td>
		</tr>

	    <tr>
	    <td>&nbsp;</td>
	    <td colspan="2"><?php echo $item['description']; ?></td>
	    <td>&nbsp;</td>
	    </tr>
            <?php
                if($item['discount']<>0){
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = ($item['price'] * $item['discount'] / 100)*$item['quantity'];
                            ?><tr>
                                <td>&nbsp;</td>
                                <td>DISCOUNT (<?php echo $item['discount_reason']; ?>)</td>
                                <td style='text-align:right;'><?php echo $item['discount'].$item['discount_type']; ?></td>
                                <td style='text-align:right;'><?php echo to_currency($myDisc*-1); ?></td>
                              </tr><?php
                            break;
                        case '£':
                        case '€':
                            $myDisc = $item['discount']*$item['quantity'];
                            ?><tr>
                                <td>&nbsp;</td>
                                <td>DISCOUNT (<?php echo $item['discount_reason']; ?>)</td>
                                <td style='text-align:right;'><?php echo to_currency($item['discount']); ?></td>
                                <td style='text-align:right;'><?php echo to_currency($myDisc*-1); ?></td>
                              </tr><?php
                            break;
                        default:
                            $myDisc = 0;
                    }
                }
            ?>

	<?php
	}
	?>
	<tr>
	<td colspan="3" style='text-align:right;border-top:2px solid #000000;'><?php echo $this->lang->line('sales_sub_total'); ?></td>
	<td style='text-align:right;border-top:2px solid #000000;'><?php echo to_currency($subtotal); ?></td>
	</tr>

	<?php foreach($taxes as $name=>$value) { ?>
		<tr>
			<td colspan="3" style='text-align:right;'><?php echo $name; ?>:</td>
			<td style='text-align:right;'><?php echo to_currency($value); ?></td>
		</tr>
	<?php }; ?>

	<tr>
	<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_total'); ?></td>
	<td style='text-align:right'><?php echo to_currency($total); ?></td>
	</tr>

    <tr><td colspan="4">&nbsp;</td></tr>

	<?php
		foreach($payments as $payment_id=>$payment)
	{ ?>
		<tr>
		<td colspan="2" style="text-align:right;"><?php echo $this->lang->line('sales_payment'); ?></td>
		<td style="text-align:right;"><?php echo  $payment['payment_type']    ?> </td>
		<td style="text-align:right"><?php echo  to_currency($payment['payment_amount'] * -1 )  ?>  </td>
	    </tr>
	<?php
	}
	?>

    <tr><td colspan="4">&nbsp;</td></tr>

	<tr>
		<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_change_due'); ?></td>
		<td style='text-align:right'><?php echo  $amount_change; ?></td>
	</tr>

	</table>

	<div id="sale_return_policy">
	<?php echo nl2br($this->config->item('vat_number').
                    "<br/>".$this->config->item('return_policy1').
                    "<br/>".$this->config->item('return_policy2').
                    "<br/>".$this->config->item('return_policy3').
                    "<br/>".$this->config->item('return_policy4').
                    "<br/>".$this->config->item('website'))
                ; ?>
	</div>
	<div id='barcode'>
	<?php echo "<img src='index.php?c=barcode&barcode=$sale_id&text=$sale_id&width=250&height=50' />"; ?>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
