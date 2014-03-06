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
		<?php if(isset($branch))
		{
		?>
			<div id="customer"><?php echo $this->lang->line('branches_branch').": ".$branch; ?></div>
		<?php
		}
		?>
		<div id="sale_id"><?php echo $this->lang->line('trans_id').": ".$transfer_id; ?></div>
		<div id="employee"><?php echo $this->lang->line('employees_employee').": ".$employee; ?></div>
	</div>

	<table id="receipt_items">
	<tr>
            <th style="width:10%;"><?php echo $this->lang->line('items_item_number'); ?></th>
            <th style="width:20%;"><?php echo $this->lang->line('items_item'); ?></th>
            <th style="width:10%;text-align:right;"><?php echo $this->lang->line('items_cost_price'); ?></th>
            <th style="width:10%;text-align:right;"><?php echo $this->lang->line('items_retail_price'); ?></th>
            <th style="width:10%;text-align:right;"><?php echo $this->lang->line('items_unit_price'); ?></th>
            <th style="width:10%;text-align:right;"><?php echo $this->lang->line('items_stock_quantity'); ?></th>
            <th style="width:10%;text-align:right;"><?php echo $this->lang->line('trans_transfer_quantity'); ?></th>
            <th style="width:10%;text-align:right;">&nbsp;</th>
	</tr>
	<?php
	foreach($cart as $item_id=>$item)
	{
	?>
		<tr>
                    <td style='text-align:right;'><?php echo $item['item_number']; ?></td>
                    <td><?php echo $item['name']; ?><br/><?php echo $item['description']; ?></td>
                    <td style='text-align:right;'><?php echo $item['cost_price']; ?></td>
                    <td style='text-align:right;'><?php echo $item['retail_price']; ?></td>
                    <td style='text-align:right;'><?php echo $item['unit_price']; ?></td>
                    <td style='text-align:right;'><?php echo $item['current_quantity']; ?></td>
                    <td style='text-align:right;'><?php echo $item['quantity']; ?></td>
                    <td style='text-align:right;'><?php echo $item['location']; ?></td>
		</tr>
	<?php
	}
	?>

	</table>

	<div id='barcode'>
	<?php echo "<img src='index.php?c=barcode&barcode=$transfer_id&text=$transfer_id&width=250&height=50' />"; ?>
	</div>
</div>
<?php $this->load->view("partial/footer"); ?>
