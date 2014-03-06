<?php
echo form_open('items/find_location_info/'.$location_info->location_id,array('id'=>'location_number_form'));
?>
<?php
echo form_close();
echo form_open('locations/save/'.$location_info->location_id,array('id'=>'location_form'));
?>
<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<fieldset id="location_basic_info">
<legend><?php echo $this->lang->line("locations_basic_information"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('locations_location_ref').':', 'location_ref',array('class'=>'required')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'location_ref',
		'id'=>'location_ref',
		'value'=>$location_info->location_ref)
	);?>
	</div>
</div>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('locations_location_comment').':', 'location_comment',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_input(array(
		'name'=>'location_comment',
		'id'=>'location_comment',
		'value'=>$location_info->location_comment)
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
	$('#location_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_location_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			location_ref: "required"
   		},
		messages:
		{
     		location_ref: "<?php echo $this->lang->line('locations_location_ref_required'); ?>"
		}
	});
});
</script>