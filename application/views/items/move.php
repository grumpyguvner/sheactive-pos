<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
<ul id="error_message_box"></ul>
<?php
echo form_open('items/save_location/'.$item_info->item_id,array('id'=>'item_form'));
?>
<fieldset id="item_location_info">
<legend><?php echo $this->lang->line("items_location_information"); ?></legend>

<div class="field_row clearfix">
<?php echo form_label($this->lang->line('items_location_ref').':', 'location',array('class'=>'wide')); ?>
	<div class='form_field'>
	<?php echo form_dropdown('location_id', $locations, $selected_location);?>
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

	$('#item_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_item_form_submit(response);
			},
			dataType:'json'
                        });

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules:
		{
			location_id:
			{
				required:true			
                        }
   		},
		messages:
		{
			location_id:
			{
				required:"<?php echo $this->lang->line('items_location_required'); ?>"
			}

		}
	});

});
</script>