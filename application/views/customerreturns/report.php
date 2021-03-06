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
		<div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
		<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
		<div id="customerreturn_receipt"><?php echo $receipt_title; ?></div>
		<div id="customerreturn_time"><?php echo $transaction_time ?></div>
	</div>
	<div id="receipt_general_info">
		<div id="customerreturn_id"><?php echo $this->lang->line('customerreturns_id').": ".$customerreturn_id; ?></div>
		<div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></div>
	</div>

	<table id="receipt_items">
	<tr>
            <th style="width:16%;"><?php echo $this->lang->line('items_id'); ?></th>
            <th style="width:68%;"><?php echo $this->lang->line('items_item'); ?></th>
            <th style="width:16%;text-align:center;"><?php echo $this->lang->line('customerreturns_quantity'); ?></th>
	</tr>
	<?php
	foreach($cart as $item_id=>$item)
	{
	?>
		<tr>
                <td align="left"><?php echo $item['item_number']; ?></td>
                    <td><span class='long_name'><?php echo $item['name']; ?></span><span class='short_name'><?php echo character_limiter($item['name'],10); ?></span></td>
                    <td style='text-align:center;'><?php echo $item['quantity']; ?></td>
		</tr>

	    <tr>
                <td align="left">&nbsp;</td>
                <td align="left"><?php echo $item['description']; ?></td>
		<td><?php echo $item['location']; ?></td>
	    </tr>
	<?php
	}
	?>

	</table>

	<div id='barcode'>
	<?php echo "<img src='index.php?c=barcode&barcode=$customerreturn_id&text=$customerreturn_id&width=250&height=50' />"; ?>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
