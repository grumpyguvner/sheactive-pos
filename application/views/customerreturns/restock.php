<ul id="error_message_box"></ul>

<?php echo form_open('customerreturns/edit_item/'.$line,array('name'=>'restock_save')); ?>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('customerreturns_reason_code').':', 'reason_code',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_dropdown('select_reason_code', array('0'=>'PLEASE SELECT','1'=>'1: Too Big','2'=>'2: Too Small','3'=>'3: Too Long','4'=>'4: Too Short','5'=>'5: Didn\'t Like Fabric','6'=>'6: Didn\'t Like Colour','7'=>'7: Wrong Goods','8'=>'8: Wasn\'t Right Look','9'=>'9: Exchange'), $reason_code,'id="select_reason_code" onchange="change_values();"');?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('customerreturns_restock').':', 'restock',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_dropdown('select_restock', array('0'=>'NO','1'=>'YES'), $restock,'id="select_restock" onchange="change_values();"');?>
	</div>
</div>

<div class="field_row clearfix">	
<?php echo form_label($this->lang->line('customerreturns_faulty').':', 'faulty',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_dropdown('select_faulty', array('0'=>'NO','1'=>'YES'), $faulty,'id="select_faulty" onchange="change_values();"');?>
	</div>
</div>

<?php echo $this->lang->line('customerreturns_comment')." :"; ?><br/>
<?php echo form_input(array('id'=>'comment','name'=>'comment','value'=>$comment,'size'=>'30')); ?>

<div class='bigger_button' id='restock_submit' style='float:right;margin-top:5px;' onclick="restock_submit()">
  <span><?php echo $this->lang->line('common_submit'); ?></span>
</div>

<?php echo form_input(array('id'=>'description','name'=>'description','value'=>$description,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'ean_upc','name'=>'ean_upc','value'=>$ean_upc,'size'=>'20','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'serialnumber','name'=>'serialnumber','value'=>$serialnumber,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'price','name'=>'price','value'=>$price,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'cost_price','name'=>'cost_price','value'=>$cost_price,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'quantity','name'=>'quantity','value'=>$quantity,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'reason_code','name'=>'reason_code','value'=>$reason_code,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'restock','name'=>'restock','value'=>$restock,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'faulty','name'=>'faulty','value'=>$faulty,'size'=>'10','style'=>'visibility:hidden;')); ?>

<?php echo form_close(); ?>

<script type="text/javascript" language="javascript">

function change_values()
{
	var myReasonSelect = document.getElementById('select_reason_code');
	var myReasonField = document.getElementById('reason_code');
	var reasonidx = myReasonSelect.selectedIndex;
	myReasonField.value = reasonidx;
	var myRestockSelect = document.getElementById('select_restock');
	var myRestockField = document.getElementById('restock');
	var restockidx = myRestockSelect.selectedIndex;
	myRestockField.value = restockidx;
	var myFaultySelect = document.getElementById('select_faulty');
	var myFaultyField = document.getElementById('faulty');
	var faultyidx = myFaultySelect.selectedIndex;
	if (restockidx==1)
	{
		faultyidx=0;
		myFaultySelect.style.visibility = "hidden";
	}else{
		myFaultySelect.style.visibility = "visible";
	}
	myFaultyField.value = faultyidx;
}

function restock_submit()
{
  var myRestockField = document.getElementById('restock');
  var bRestock = myRestockField.value;
  var myCommentField = document.getElementById('comment');
  var strComment = myCommentField.value;
  if (bRestock != 1 && strComment == ""){
    alert('<?php echo $this->lang->line('customerreturns_comment_required'); ?>');
  }else{
    document.restock_save.submit();
  }
}

change_values();

</script>
