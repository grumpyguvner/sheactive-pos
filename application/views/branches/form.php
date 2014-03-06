<?php
echo form_open('branches/save/'.$branch_info->branch_ref,array('id'=>'branch_form'));
?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<fieldset id="branch_basic_info">
<legend><?php echo $this->lang->line("customers_basic_information"); ?></legend>
<div class="field_row clearfix">
<?php echo form_label($this->lang->line('branches_branch_name').':', 'branch_name',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'branch_name',
		'id'=>'branch_name',
		'value'=>$branch_info->branch_name)
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('branches_branch_ref').':', 'branch_ref',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'branch_ref',
		'id'=>'branch_ref',
		'value'=>$branch_info->branch_ref)
	);?>
	</div>
</div>

<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'submit_button float_right')
);
?>
</fieldset>
<?php
echo form_close();
?>
<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{
	$('#branch_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_branch_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			branch_name: "required",
			branch_ref: "required"
   		},
		messages:
		{
     		branch_name: "<?php echo $this->lang->line('branches_branch_name_required'); ?>",
     		branch_ref: "<?php echo $this->lang->line('branches_branch_ref_required'); ?>",
		}
	});
});
</script>