<ul id="error_message_box"></ul>

<?php echo form_open('customerreturns/payment_save',array('name'=>'payment_save')); ?>

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
        <div class='payment_display' id='payment_display' style='float:right;margin-top:5px;'>
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
        <div class='bigger_button' id='payment_cash' style='float:right;margin-top:5px;' onclick="set_type('CASH')">
                <span><?php echo "CASH"; ?></span>
        </div>
        <div class='bigger_button' id='payment_card' style='float:right;margin-top:5px;' onclick="set_type('CARD')">
                <span><?php echo "CARD"; ?></span>
        </div>
        <div class='bigger_button' id='payment_gift' style='float:right;margin-top:5px;' onclick="set_type('VOUCHER')">
                <span><?php echo "VOUCHER"; ?></span>
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
</tr>
</table>
<?php echo form_input(array('id'=>'amount_due','name'=>'amount_due','value'=>$amount_due,'size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'amount_tendered','name'=>'amount_tendered','value'=>'','size'=>'10','style'=>'visibility:hidden;')); ?>
<?php echo form_input(array('id'=>'payment_type','name'=>'payment_type','value'=>'','size'=>'10','style'=>'visibility:hidden;')); ?>

<?php echo form_close(); ?>

<script type='text/javascript'>
function digit($value)
{
  var myTextField = document.getElementById('amount_tendered');
  if($value=="C"){
    myTextField.value = "";
  }else{
    myTextField.value = myTextField.value+$value;
  }
  if(myTextField.value == ""){
    var numVal = 0;
  }else{
    var numVal = parseFloat(myTextField.value)/100;
  }
  var myDisplayField = document.getElementById('payment_display');
  myDisplayField.innerHTML = "<span>"+numVal.toFixed(2)+"</span>";
}
function set_type($value)
{
  var myTextField = document.getElementById('payment_type');
  myTextField.value = $value;
  var myTextField = document.getElementById('amount_tendered');
  if(myTextField.value == ""){
    var numVal = parseFloat(document.getElementById('amount_due').value);
  }else{
    var numVal = parseFloat(myTextField.value)/100;
    if(parseFloat(document.getElementById('amount_due').value)<0)
        numVal=numVal*-1;
  }
  if(numVal==0){
    alert('No amount has been entered!');
  }else{
    myTextField.value = numVal;
    document.payment_save.submit();
  }
}
</script>
