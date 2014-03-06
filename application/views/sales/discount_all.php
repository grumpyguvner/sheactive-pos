<ul id="error_message_box"></ul>

<?php echo form_open('sales/discount_all_apply',array('name'=>'discount_save')); ?>

<table width="100%">
<tr>
    <td>
        <div class='bigger_button' id='add_digit_7' style='float:left;margin-top:5px;' onclick="digit('7')">
                <span><?php echo "7"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_8' style='float:left;margin-top:5px;' onclick="digit('8')">
                <span><?php echo "8"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_9' style='float:left;margin-top:5px;' onclick="digit('9')">
                <span><?php echo "9"; ?></span>
        </div>
    </td>
    <td>
        <div class='discount_display' id='discount_display' style='float:right;margin-top:5px;'>
                <span>0.00</span>
        </div>
    </td>
</tr>
<tr>
    <td>
        <div class='bigger_button' id='add_digit_4' style='float:left;margin-top:5px;' onclick="digit('4')">
                <span><?php echo "4"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_5' style='float:left;margin-top:5px;' onclick="digit('5')">
                <span><?php echo "5"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_6' style='float:left;margin-top:5px;' onclick="digit('6')">
                <span><?php echo "6"; ?></span>
        </div>
    </td>
    <td>
        <div class='bigger_button' id='discount_cash' style='float:right;margin-top:5px;' onclick="set_type('£')">
                <span><?php echo "£"; ?></span>
        </div>
        <div class='bigger_button' id='discount_percent' style='float:right;margin-top:5px;' onclick="set_type('%')">
                <span><?php echo "%"; ?></span>
        </div>
    </td>
</tr>
<tr>
    <td>
        <div class='bigger_button' id='add_digit_1' style='float:left;margin-top:5px;' onclick="digit('1')">
                <span><?php echo "1"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_2' style='float:left;margin-top:5px;' onclick="digit('2')">
                <span><?php echo "2"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_3' style='float:left;margin-top:5px;' onclick="digit('3')">
                <span><?php echo "3"; ?></span>
        </div>
    </td>
    <td>
        <?php echo $this->lang->line('sales_discount_reason')." :"; ?><br/>
        <?php echo form_input(array('id'=>'discount_reason','name'=>'discount_reason','value'=>$discount_reason,'size'=>'20')); ?>
    </td>
</tr>
<tr>
    <td>
        <div class='bigger_button' id='cancel' style='float:left;margin-top:5px;' onclick="digit('C')">
                <span><?php echo "C"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_0' style='float:left;margin-top:5px;' onclick="digit('0')">
                <span><?php echo "0"; ?></span>
        </div>
        <div class='bigger_button' id='add_digit_00' style='float:left;margin-top:5px;' onclick="digit('00')">
                <span><?php echo "00"; ?></span>
        </div>
    </td>
    <td>
        <div class='bigger_button' id='discount_submit' style='float:right;margin-top:5px;' onclick="discount_submit()">
                <span><?php echo $this->lang->line('common_submit'); ?></span>
        </div>
    </td>
</tr>
</table>
<?php echo form_input(array('id'=>'discount_type','name'=>'discount_type','value'=>$discount_type,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'discount','name'=>'discount','value'=>$discount,'size'=>'10','style'=>'visibility:hidden;')); ?>

<?php echo form_close(); ?>

<script type="text/javascript" language="javascript">

function discount_submit()
{
  var myAmountField = document.getElementById('discount');
  var numVal = parseFloat(myAmountField.value);
  var myReasonField = document.getElementById('discount_reason');
  var strReason = myReasonField.value;
  if (numVal != 0 && strReason == ""){
    alert('<?php echo $this->lang->line('sales_discount_reason_required'); ?>');
  }else{
    document.discount_save.submit();
  }
}
function display_discount()
{
  var myAmountField = document.getElementById('discount');
  if(myAmountField.value == ""){
    myAmountField.value = "0";
  }
  var numVal = parseFloat(myAmountField.value);
  var myTypeField = document.getElementById('discount_type');
  var myDisplayField = document.getElementById('discount_display');
  if(myTypeField.value == "£"){
      myDisplayField.innerHTML = "<span>"+myTypeField.value+" "+numVal.toFixed(2)+"</span>";
  }else{
      myDisplayField.innerHTML = "<span>"+numVal.toFixed(2)+" "+myTypeField.value+"</span>";
  }
}
function digit($value)
{
  var myAmountField = document.getElementById('discount');
  if($value=="00"){
    var numVal = parseInt(myAmountField.value*10000);
  }else{
    var numVal = parseInt(myAmountField.value*1000);
  }
  if($value=="C"){
    numVal = 0;
  }else{
    numVal = (numVal+parseInt($value))/100;
  }
  myAmountField.value = numVal.toFixed(2);
  display_discount();
}
function set_type($value)
{
  document.getElementById('discount_type').value = $value;
  display_discount();
}

display_discount();

</script>
