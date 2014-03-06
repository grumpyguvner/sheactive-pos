<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('sales_register'); ?></div>
<?php
if(isset($error))
{
	echo "<div id='error' class='error_message'>".$error."</div>";
}

if (isset($warning))
{
	echo "<div class='warning_mesage'>".$warning."</div>";
}
?>
<div id="register_wrapper">
<?php echo form_open("sales/change_mode",array('id'=>'mode_form')); ?>
	<span><?php echo $this->lang->line('sales_mode') ?></span>
<?php echo form_dropdown('mode',$modes,$mode,'id="mode" onchange="$(\'#mode_form\').submit();"'); ?>
<?php echo form_close() ?>


<?php echo form_open("sales/add_plu",array('id'=>'add_plu_form')); ?>
<label id="item_label" for="item">
<?php
if($mode=='sale')
{
	echo $this->lang->line('sales_find_or_scan_plu');
}
else
{
	echo $this->lang->line('sales_find_or_scan_plu_or_receipt');
}
?>
</label>
<?php echo form_input(array('name'=>'plu','id'=>'plu','size'=>'40'));?>
<?php echo form_close() ?>



<?php echo form_open("sales/add",array('id'=>'add_item_form')); ?>
<label id="item_label" for="item">

<?php
if($mode=='sale')
{
	echo $this->lang->line('sales_find_or_scan_item');
}
else
{
	echo $this->lang->line('sales_find_or_scan_item_or_receipt');
}
?>
</label>
<?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40'));?>
<?php if ($items_module_allowed){ ?>
    <div id="new_item_button_register" >
        <?php echo anchor("items/view/-1/width:800",
        "<div class='small_button'><span>".$this->lang->line('sales_new_item')."</span></div>",
        array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_item')));
        ?>
    </div>
<?php } ?>
<?php echo form_close() ?>

<table id="register">
<thead>
<tr>
<th>&nbsp;</th>
<th><?php echo $this->lang->line('sales_item_number'); ?></th>
<th><?php echo $this->lang->line('sales_item_name'); ?></th>
<th><?php echo $this->lang->line('sales_price'); ?></th>
<th colspan="2"><?php echo $this->lang->line('sales_discount'); ?></th>
<th><?php echo $this->lang->line('sales_total'); ?></th>
<th>&nbsp;</th>
</tr>
</thead>
<tbody id="cart_contents">
<?php
if(count($cart)==0)
{
?>
<tr><td colspan='8'>
<div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
</td></tr>
<?php
}
else
{
		foreach($cart as $line=>$item)
	{
		echo form_open("sales/edit_item/$line");
        	if($item['quantity']<0)
        	{
        		echo "<tr><td colspan=8 style='text-align:left;'>".$this->lang->line('sales_returned_item')."</td></tr>";
        	}
        	?>
		<tr>
		<td rowspan="2"><?php echo anchor("sales/delete_item/$line",'<h3 class="delete_btn">['.$this->lang->line('common_delete').']</h3>');?></td>
                <td>
                    <?php echo anchor("items/view/".$item['item_id']."/width:800",
                    $item['item_number'],
                    array('class'=>'thickbox none','title'=>$this->lang->line('sales_edit_item')));
                    ?>
                </td>
		<td style="text-align: left;"><?php echo $item['name']; ?></td>



		<?php if ($items_module_allowed)
		{
		?>
			<td><?php echo form_input(array('name'=>'price','value'=>$item['price'],'size'=>'6'));?></td>
		<?php
		}
		else
		{
		?>
			<td><?php echo $item['price']; ?></td>
			<?php echo form_hidden('price',$item['price']); ?>
		<?php
		}
		?>
		<?php echo form_hidden('quantity',$item['quantity']); ?>

		<td>
                <?php echo anchor("sales/discount_item/$line/width:800",'<h3 class="discount_btn">['.$this->lang->line('sales_discount').']</h3>', array('class'=>'thickbox none','title'=>$this->lang->line('sales_discount')));?>
                </td>
		<td>
                <?php
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = $item['price'] * $item['discount'] / 100;
                            echo $item['discount']." ".$item['discount_type'];
                            break;
                        case '£':
                            $myDisc = $item['discount'];
                            echo $item['discount_type']." ".$item['discount'];
                            break;
                        case '€':
                            $myDisc = $item['discount'];
                            echo $item['discount']." ".$item['discount_type'];
                            break;
                        default:
                            $myDisc = 0;
                            echo "-";
                    }
                    echo form_hidden(array('name'=>'discount','value'=>$item['discount'],'size'=>'3'));
                    echo form_hidden(array('name'=>'discount_type','value'=>$item['discount_type'],'size'=>'1'));
                    echo form_hidden(array('name'=>'discount_reason','value'=>$item['discount_reason'],'size'=>'30'));
                ?>
                </td>

                <td><?php echo to_currency(($item['price']-$myDisc)*$item['quantity']); ?></td>
		<td rowspan="2">
                    <?php if ($items_module_allowed){ ?>
                        <?php echo form_submit("edit_item", $this->lang->line('sales_edit_item'), "id='edit_item'");?>
                    <?php } ?>
                </td>
		</tr>
		<tr>
                <td>&nbsp;</td>
		<td colspan=2 style="text-align:left;">

		<?php
        	if($item['allow_alt_description']==1)
        	{
        		echo form_input(array('name'=>'description','value'=>$item['description'],'size'=>'20'));
        	}
        	else
        	{
				if ($item['description']!='')
				{
					echo $item['description'];
        			echo form_hidden('description',$item['description']);
        		}
        		else
        		{
        			echo 'None';
        			echo form_hidden('description','');
        		}
        	}
		?>
		</td>
		<td>&nbsp;</td>
		<td style="color:#2F4F4F";>
		<?php
        	if($item['is_serialized']==1)
        	{
				echo $this->lang->line('sales_serial').':';
			}
		?>
		</td>
		<td colspan=2 style="text-align:left;">
		<?php
        	if($item['is_serialized']==1)
        	{
        		echo form_input(array('name'=>'serialnumber','value'=>$item['serialnumber'],'size'=>'20'));
			}
		?>
		</td>


		</tr>
		<tr style="height:3px">
		<td colspan=8 style="background-color:white"> </td>
		</tr>
                <?php echo form_close();
	}
}
?>
</tbody>
</table>
</div>


<div id="overall_sale">
	<?php
	if(isset($customer))
	{
		echo $this->lang->line("sales_customer").': <b>'.$customer. '</b><br />';
		echo anchor("sales/delete_customer",'['.$this->lang->line('common_delete').' '.$this->lang->line('sales_remove_customer').']');
	}
	else
	{
		echo form_open("sales/select_customer",array('id'=>'select_customer_form')); ?>
		<label id="customer_label" for="customer">
                <?php if ($customer_req)
                {
                    echo $this->lang->line('sales_select_customer_required');
                }else{
                    echo $this->lang->line('sales_select_customer');
                }?>
                </label>
		<?php echo form_input(array('name'=>'customer','id'=>'customer','size'=>'30','value'=>$this->lang->line('sales_start_typing_customer_name')));?>
                <?php echo form_close(); ?>
		<div style="margin-top:5px;text-align:center;">
		<h3 style="margin: 5px 0 5px 0"><?php echo $this->lang->line('common_or'); ?></h3>
		<?php echo anchor("customers/view/-1/width:350",
		"<div class='big_button' style='margin:0 auto;'><span>".$this->lang->line('sales_new_customer')."</span></div>",
		array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_customer')));
		?>
		</div>
		<div class="clearfix">&nbsp;</div>
		<div style="margin-top:5px;text-align:center;">
                <?php echo anchor("sales/discount_all/width:800",'<div class="bigger_button" style="margin:0 auto;"><span>'.$this->lang->line('sales_discount_all').'</span></div>', array('class'=>'thickbox none','title'=>$this->lang->line('sales_discount_all')));?>
		</div>
		<div class="clearfix">&nbsp;</div>
		<?php
	}
	?>

	<div id='sale_details'>
		<div class="float_left" style="width:55%;"><?php echo $this->lang->line('sales_sub_total'); ?>:</div>
		<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($subtotal); ?></div>

		<?php foreach($taxes as $name=>$value) { ?>
		<div class="float_left" style='width:55%;'><?php echo $name; ?>:</div>
		<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($value); ?></div>
		<?php }; ?>

		<div class="float_left" style='width:55%;'><?php echo $this->lang->line('sales_total'); ?>:</div>
		<div class="float_left" style="width:45%;font-weight:bold;"><?php echo to_currency($total); ?></div>
	</div>




	<?php
	// Only show this part if there are Items already in the sale.
	if(count($cart) > 0)
	{
	?>

            <div id="Cancel_sale">
		<?php echo form_open("sales/cancel_sale",array('id'=>'cancel_sale_form')); ?>
                    <?php if($amount_due < 0)
                    {
                        echo anchor("sales/add_payment/width:800", "<div class='bigger_button' style='float:left;margin-top:5px;'><span>".$this->lang->line('sales_add_refund')."</span></div>", array('class'=>'thickbox none','title'=>$this->lang->line('sales_add_refund')));
                    }else{
                        echo anchor("sales/add_payment/width:800", "<div class='bigger_button' style='float:left;margin-top:5px;'><span>".$this->lang->line('sales_add_payment')."</span></div>", array('class'=>'thickbox none','title'=>$this->lang->line('sales_add_payment')));
                    } ?>
                    <div class='bigger_button' id='cancel_sale_button' style='float:left;margin-top:5px;'>
                            <span><?php echo $this->lang->line('sales_cancel_sale'); ?></span>
                    </div>
                <?php echo form_close(); ?>
            </div>
            <div class="clearfix" style="margin-bottom:1px;">&nbsp;</div>

    <table width="100%"><tr>
    <td style="width:55%; "><div class="float_left"><?php echo 'Payments Total:' ?></div></td>
    <td style="width:45%; text-align:right;"><div class="float_left" style="text-align:right;font-weight:bold;"><?php echo to_currency($payments_total); ?></div></td>
	</tr>
	<tr>
	<td style="width:55%; "><div class="float_left" ><?php echo 'Amount Due:' ?></div></td>
	<td style="width:45%; text-align:right; "><div class="float_left" style="text-align:right;font-weight:bold;"><?php echo to_currency($amount_due); ?></div></td>
	</tr></table>

	<div id="Payment_Types" >

		<?php
		// Only show this part if there is at least one payment entered.
		if(count($payments) > 0)
		{
		?>
	    	<table id="register">
	    	<thead>
			<tr>
			<th style="width:11%;"><?php echo $this->lang->line('common_delete'); ?></th>
			<th style="width:60%;"><?php echo 'Type'; ?></th>
			<th style="width:18%;"><?php echo 'Amount'; ?></th>


			</tr>
			</thead>
			<tbody id="payment_contents">
			<?php
				foreach($payments as $payment_id=>$payment)
				{
				echo form_open("sales/edit_payment/$payment_id",array('id'=>'edit_payment_form'.$payment_id));
				?>
	            <tr>
	            <td><?php echo anchor("sales/delete_payment/$payment_id",'['.$this->lang->line('common_delete').']');?></td>


				<td><?php echo  $payment['payment_type']    ?> </td>
				<td style="text-align:right;"><?php echo  to_currency($payment['payment_amount'])  ?>  </td>


				</tr>
				<?php echo form_close();
				}
				?>
			</tbody>
			</table>
		    <br>
                <div id="finish_sale">
                        <?php echo form_open("sales/complete",array('id'=>'finish_sale_form')); ?>
                        <label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
                        <?php echo form_textarea(array('name'=>'comment','value'=>'','rows'=>'4','cols'=>'23'));?>
                        <br><br>

                        <?php echo "<div class='small_button' id='finish_sale_button' style='float:right;margin-top:5px;'><span>".$this->lang->line('sales_complete_sale')."</span></div>"; ?>
                </div>
                <?php echo form_close(); ?>
                <?php } ?>
	</div>

	<?php
        } else { ?>
            <div id="No_sale">
                    <?php echo form_open("sales/no_sale",array('id'=>'no_sale_form')); ?>
                    <div class='big_button' id='no_sale_button' style='margin:5px auto;'>
                            <span><?php echo $this->lang->line('sales_no_sale'); ?></span>
                    </div>
            <?php echo form_close(); ?>
            </div>
	<?php
	}
	?>
</div>
<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>


<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{
    $("#item").autocomplete('<?php echo site_url("sales/item_search"); ?>',
    {
    	minChars:0,
    	max:100,
    	selectFirst: false,
       	delay:10,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#item").result(function(event, data, formatted)
    {
		$("#add_item_form").submit();
    });

<?php if(isset($error))
{
        echo "alert('".$error."');";
}
?>

	$('#plu').focus();

	$('#item').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#item,#customer').click(function()
    {
    	$(this).attr('value','');
    });

    $("#customer").autocomplete('<?php echo site_url("sales/customer_search"); ?>',
    {
    	minChars:0,
    	delay:10,
    	max:100,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#customer").result(function(event, data, formatted)
    {
		$("#select_customer_form").submit();
    });

    $('#customer').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_customer_name'); ?>");
    });

    $("#finish_sale_button").click(function()
    {
        if (confirm('<?php echo $this->lang->line("sales_confirm_finish_sale"); ?>'))
        {
                $('#finish_sale_form').submit();
        }
    });

    $("#no_sale_button").click(function()
    {
        $('#no_sale_form').submit();
    });

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("sales_confirm_cancel_sale"); ?>'))
    	{
    		$('#cancel_sale_form').submit();
    	}
    });

	$("#add_payment_button").click(function()
	{
	   $('#add_payment_form').submit();
    });

});

function post_item_form_submit(response)
{
	if(response.success)
	{
		$("#item").attr("value",response.item_id);
		$("#add_item_form").submit();
	}
}

function post_person_form_submit(response)
{
	if(response.success)
	{
		$("#customer").attr("value",response.person_id);
		$("#select_customer_form").submit();
	}
}

</script>
