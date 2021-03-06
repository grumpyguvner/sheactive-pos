<?php $this->load->view("partial/header"); ?>

<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('trans_register'); ?></div>

<?php
if(isset($error))
{
	echo "<div class='error_message'>".$error."</div>";
}
?>



<div id="register_wrapper">
	<?php echo form_open("transfers/change_mode",array('id'=>'mode_form')); ?>
		<span><?php echo $this->lang->line('trans_mode') ?></span>
	<?php echo form_dropdown('mode',$modes,$mode,'onchange="$(\'#mode_form\').submit();"'); ?>
	</form>

	<?php echo form_open("transfers/add_plu",array('id'=>'add_plu_form')); ?>
	<label id="item_label" for="item">

	<?php
	if($mode=='return')
	{
		echo $this->lang->line('trans_find_or_scan_plu');
	}
	else
	{
		echo $this->lang->line('trans_find_or_scan_plu_or_receipt');
	}
	?>
	</label>
<?php echo form_input(array('name'=>'plu','id'=>'plu','size'=>'40'));?>
<div id="new_item_button_register" >
		<?php echo anchor("items/view/-1/width:360",
		"<div class='small_button'><span>".$this->lang->line('sales_new_item')."</span></div>",
		array('class'=>'thickbox none','title'=>$this->lang->line('sales_new_item')));
		?>
	</div>

<?php echo form_close();?>

	<?php echo form_open("transfers/add",array('id'=>'add_item_form')); ?>
	<label id="item_label" for="item">
	<?php echo $this->lang->line('trans_find_or_scan_item'); ?>
	</label>
	<?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40'));?>

	<?php echo form_close();?>

<!-- Transfer Items List -->

<table id="register">
<thead>
<tr>
<th style="width:11%;"><?php echo $this->lang->line('common_delete'); ?></th>

<th style="width:11%;"><?php echo $this->lang->line('trans_item_number'); ?></th>
<th style="width:62%;"><?php echo $this->lang->line('trans_item_name'); ?></th>
<th style="width:5%;"><?php echo $this->lang->line('trans_quantity'); ?></th>
<th style="width:11%;"><?php echo $this->lang->line('trans_edit'); ?></th>
</tr>
</thead>
<tbody id="cart_contents">
<?php
if(count($cart)==0)
{
?>
<tr><td colspan='8'>
<div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
</tr></tr>
<?php
}
else
{
	foreach($cart as $item_id=>$item)
	{
		echo form_open("transfers/edit_item/$item_id");
	?>
		<tr>
		<td><?php echo anchor("transfers/delete_item/$item_id",'['.$this->lang->line('common_delete').']');?></td>

                <td>
                    <?php echo anchor("items/view/".$item['item_id']."/width:800",
                    $item['item_number'],
                    array('class'=>'thickbox none','title'=>$this->lang->line('receivings_edit_item')));
                    ?><br>
                    <?php
                    if($item['ean_upc']=="")
                    {
                        echo anchor("transfers/capture_ean/".$item_id."/width:800",
                        "EAN CODE MISSING",
                        array('class'=>'thickbox none','title'=>$this->lang->line('receivings_please_scan_ean')));
                    }else{
                        echo $item['ean_upc'];
                    }
                    echo form_hidden('ean_upc',$item['ean_upc']);
                    ?>

                </td>
		<td style="align:left;"><?php echo $item['name']; ?><br>

		<?php
        	if($item['allow_alt_description']==1 && false)
        	{
        		echo form_input(array('name'=>'description','value'=>$item['description'],'size'=>'20'));
        	}
        	else
        	{
				echo $item['description'];
        		echo form_hidden('description',$item['description']);
        	}
		?>
		<br>



		<?php
        	if($item['is_serialized']==1 && false)
        	{
				echo form_input(array('name'=>'serialnumber','value'=>$item['serialnumber'],'size'=>'20'));
			}
		?></td>

		<td>
		<?php
        	if($item['is_serialized']==1 && false)
        	{
        		echo $item['quantity'];
        		echo form_hidden('quantity',$item['quantity']);
        	}
        	else
        	{
        		echo form_input(array('name'=>'quantity','value'=>$item['quantity'],'size'=>'2'));
        	}
		?>
		</td>

		<td><?php echo form_submit("edit_item", $this->lang->line('sales_edit_item'));?></td>
		</tr>
		</form>
	<?php
	}
}
?>
</tbody>
</table>
</div>

<!-- Overall Transfer -->

<div id="overall_sale">
	<?php
	if(isset($branch))
	{
		echo $this->lang->line("trans_branch").': <b>'.$branch. '</b><br />';
		echo anchor("transfers/delete_branch",'['.$this->lang->line('common_delete').' '.$this->lang->line('branchs_branch').']');
 	}
	else
	{
		echo form_open("transfers/select_branch",array('id'=>'select_branch_form')); ?>
		<label id="branch_label" for="branch"><?php echo $this->lang->line('trans_select_branch'); ?></label>
		<?php echo form_input(array('name'=>'branch','id'=>'branch','size'=>'30','value'=>$this->lang->line('trans_start_typing_branch_name')));?>
		</form>
                <?php if ($branches_module_allowed){ ?>
                    <div style="margin-top:5px;text-align:center;">
                    <h3 style="margin: 5px 0 5px 0"><?php echo $this->lang->line('common_or'); ?></h3>
                    <?php echo anchor("branchs/view/-1/width:350",
                    "<div class='small_button' style='margin:0 auto;'><span>".$this->lang->line('trans_new_branch')."</span></div>",
                    array('class'=>'thickbox none','title'=>$this->lang->line('trans_new_branch')));
                    ?>
                    </div>
		<?php } ?>
		<div class="clearfix">&nbsp;</div>
		<?php
	}
	?>

	<?php
	if(count($cart) > 0)
	{
	?>
	<div id="finish_sale">
		<?php echo form_open("transfers/complete",array('id'=>'finish_sale_form')); ?>
       		<?php echo form_hidden(array('name'=>'branch_ref','value'=>$this->transfer_lib->get_branch())); ?>
		<br>
		<label id="comment_label" for="comment"><?php echo $this->lang->line('common_comments'); ?>:</label>
		<?php echo form_textarea(array('name'=>'comment','value'=>'','rows'=>'4','cols'=>'23'));?>
		<br><br>

		<?php echo "<div class='small_button' id='finish_sale_button' style='float:right;margin-top:5px;'><span>".$this->lang->line('trans_complete_transfer')."</span></div>";
		?>
		</div>

		</form>

	    <?php echo form_open("transfers/cancel_transfer",array('id'=>'cancel_sale_form')); ?>
			    <div class='small_button' id='cancel_sale_button' style='float:left;margin-top:5px;'>
					<span>Cancel </span>
				</div>
        </form>
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
    $("#item").autocomplete('<?php echo site_url("transfers/item_search"); ?>',
    {
    	minChars:0,
    	max:100,
       	delay:10,
       	selectFirst: false,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#item").result(function(event, data, formatted)
    {
		$("#add_item_form").submit();
    });

	$('#plu').focus();

	$('#item').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#item,#branch').click(function()
    {
    	$(this).attr('value','');
    });

    $("#branch").autocomplete('<?php echo site_url("transfers/branch_search"); ?>',
    {
    	minChars:0,
    	delay:10,
    	max:100,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#branch").result(function(event, data, formatted)
    {
		$("#select_branch_form").submit();
    });

    $('#branch').blur(function()
    {
    	$(this).attr('value',"<?php echo $this->lang->line('trans_start_typing_branch_name'); ?>");
    });

    $("#finish_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("trans_confirm_finish_transfer"); ?>'))
    	{
    		$('#finish_sale_form').submit();
    	}
    });

    $("#cancel_sale_button").click(function()
    {
    	if (confirm('<?php echo $this->lang->line("trans_confirm_cancel_transfer"); ?>'))
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

function post_person_form_submit(response)
{
	if(response.success)
	{
		$("#branch").attr("value",response.person_id);
		$("#select_branch_form").submit();
	}
}

</script>