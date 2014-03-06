<ul id="error_message_box"></ul>

<?php echo form_open('stocktakes/edit_item/'.$line,array('name'=>'capture_ean')); ?>

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
                <span></span>
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
        &nbsp;
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
<?php echo form_input(array('id'=>'description','name'=>'description','value'=>$description,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'ean_upc','name'=>'ean_upc','value'=>$ean_upc,'size'=>'20','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'serialnumber','name'=>'serialnumber','value'=>$serialnumber,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'quantity','name'=>'quantity','value'=>$quantity,'size'=>'10','style'=>'visibility:hidden;')); ?>

<?php echo form_close(); ?>

<script type="text/javascript" language="javascript">
document.onkeyup = KeyCheck;

function KeyCheck(e)
{
   var KeyID = (window.event) ? event.keyCode : e.keyCode;

   switch(KeyID)
   {
      case 16: // "Shift"
      case 17: // "Ctrl"
      case 18: // "Alt"
      case 19: // "Pause"
      case 37: // "Arrow Left"
      case 38: // "Arrow Up"
      case 39: // "Arrow Right"
      case 40: // "Arrow Down"
        void(0);
        break;
      case 48: // "0"
      case 49: // "1"
      case 50: // "2"
      case 51: // "3"
      case 52: // "4"
      case 53: // "5"
      case 54: // "6"
      case 55: // "7"
      case 56: // "8"
      case 57: // "9"
        digit(String.fromCharCode(KeyID));
        break;
      case 27: // "ESC"
      case 67: // "c"
        digit("C");
        break;
      case 13: // "ENTER"
        discount_submit();
        break;
   }

}

function discount_submit()
{
  //NO VALIDATION??
  document.capture_ean.submit();
}
function display_discount()
{
  var myAmountField = document.getElementById('ean_upc');
  var myDisplayField = document.getElementById('discount_display');
  myDisplayField.innerHTML = "<span>"+myAmountField.value+"</span>";
}
function digit($value)
{
  var myAmountField = document.getElementById('ean_upc');
  if($value=="C"){
    myAmountField.value = "";
  }else{
    myAmountField.value = myAmountField.value+$value;
  }
  display_discount();
}

display_discount();

</script>
