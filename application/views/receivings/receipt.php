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
		<div id="sale_receipt"><?php echo $receipt_title; ?></div>
		<div id="sale_time"><?php echo $transaction_time ?></div>
	</div>
	<div id="receipt_general_info">
		<?php if(isset($supplier))
		{
		?>
			<div id="customer"><?php echo $this->lang->line('suppliers_supplier').": ".$supplier; ?></div>
		<?php
		}
		?>
		<div id="sale_id"><?php echo $this->lang->line('recvs_id').": ".$receiving_id; ?></div>
		<div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></div>
	</div>

	<table id="receipt_items">
	<tr>
	<th style="width:10%;"><?php echo $this->lang->line('items_item_number'); ?></th>
	<th style="width:25%;"><?php echo $this->lang->line('items_item'); ?></th>
	<th style="width:25%;"><?php echo $this->lang->line('items_description'); ?></th>
	<th style="width:10%;"><?php echo $this->lang->line('items_cost_price'); ?></th>
	<th style="width:10%;text-align:center;"><?php echo $this->lang->line('common_quantity'); ?></th>
	<th style="width:20%;text-align:right;"><?php echo $this->lang->line('items_location_ref'); ?></th>
	</tr>
	<?php
	foreach($cart as $item_id=>$item)
	{
	?>
		<tr>
		<td style='text-align:right;'><?php echo $item['item_number']; ?></td>
		<td><span class='long_name'><?php echo $item['name']; ?></span><span class='short_name'><?php echo character_limiter($item['name'],30); ?></span></td>
		<td><span class='long_name'><?php echo $item['description']; ?></span><span class='short_name'><?php echo character_limiter($item['description'],30); ?></span></td>
		<td style='text-align:right;'><?php echo to_currency($item['cost_price']); ?></td>
		<td style='text-align:center;'><?php echo $item['quantity']; ?></td>
		<td style='text-align:right;'><?php echo $item['location']; ?></td>
		</tr>
	<?php
	}
	?>
	<tr>
	<td colspan="3" style='text-align:right;'><?php echo $this->lang->line('sales_total'); ?></td>
	<td style='text-align:right'><?php echo to_currency($subtotal); ?></td>
	<td colspan="2">&nbsp;</td>
	</tr>

	</table>

	<div id='barcode'>
	<?php echo "<img src='index.php?c=barcode&barcode=$receiving_id&text=$receiving_id&width=250&height=50' />"; ?>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
