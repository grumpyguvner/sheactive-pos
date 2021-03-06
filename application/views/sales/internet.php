<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('sales_internet'); ?></div>
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
        <?php echo anchor("items/view/-1/width:360",
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
<th colspan="3"><?php echo $this->lang->line('sales_quantity'); ?></th>
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
        		echo "<tr><td colspan=9 style='text-align:left;'>".$this->lang->line('sales_returned_item')."</td></tr>";
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



                <td><?php echo $item['price']; ?></td>
                <?php echo form_hidden('price',$item['price']); ?>

                <td>&nbsp;
                </td>
                <td><?php echo $item['quantity']; ?></td>
		<?php echo form_hidden('quantity',$item['quantity']); ?>
		<td>
                <?php
                    switch ($item['discount_type']){
                        case '%':
                            $myDisc = $item['price'] * $item['discount'] / 100;
                            break;
                        case '£':
                            $myDisc = $item['discount'];
                            break;
                        case '€':
                            $myDisc = $item['discount'];
                            break;
                        default:
                            $myDisc = 0;
                    }
                    echo form_hidden(array('name'=>'discount','value'=>$item['discount'],'size'=>'3'));
                    echo form_hidden(array('name'=>'discount_type','value'=>$item['discount_type'],'size'=>'1'));
                    echo form_hidden(array('name'=>'discount_reason','value'=>$item['discount_reason'],'size'=>'30'));
                ?>
                </td>

                <td><?php echo to_currency(($item['price']-$myDisc)*$item['quantity']); ?></td>
                <td rowspan="2">&nbsp;
                </td>
		</tr>
		<tr>
                <td>
        	<?php
                if($item['ean_upc']=="")
                {
                    echo anchor("sales/capture_ean/".$line."/width:800",
                    "EAN CODE MISSING",
                    array('class'=>'thickbox none','title'=>$this->lang->line('sales_please_scan_ean')));
                }else{
                    echo $item['ean_upc'];
                }
                echo form_hidden('ean_upc',$item['ean_upc']);
                ?>
                </td>
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

	<div id="Payment_Types" >

                <div id="finish_sale">
                        <?php echo form_open("sales/complete",array('id'=>'finish_sale_form')); ?>
                        <label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
                        <?php echo form_textarea(array('name'=>'comment','value'=>'','rows'=>'4','cols'=>'23'));?>
                        <br><br>

                        <?php echo "<div class='small_button' id='finish_sale_button' style='float:right;margin-top:5px;'><span>".$this->lang->line('sales_complete_sale')."</span></div>"; ?>
                </div>
                <?php echo form_close(); ?>
	</div>

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

});

function post_item_form_submit(response)
{
	if(response.success)
	{
		$("#item").attr("value",response.item_id);
		$("#add_item_form").submit();
	}
}

</script>
