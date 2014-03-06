<?php
    $lp = fopen("/dev/lp0", 'a');

    fwrite($lp, "\x01*");
    //sleep for 2 second to allow command to register
    usleep(2000000);
    $labelFormat = "\x02BPY\x0D"; //enable pound sign
    $labelFormat .= "\x02ySUK\x0D"; //select UK ISO 4 character set
    $labelReset = "\x02LD11";
    $field01Format = "191200200650010";
    $field02Format = "191100200540010";
    $field03Format = "191100200450010";
    $field04Format = "1F2203500000020";

    foreach($cart as $line=>$item)
    {
        if($item['quantity']<0)
            $item['quantity'] = $item['quantity']*-1;
        $labelFormat .= $labelReset;
        $labelFormat .= $field01Format.str_pad(substr("\x23 ".number_format($item['unit_price'],2),0,8), 28, " ", STR_PAD_BOTH)."\x0D";
        $labelFormat .= $field02Format.str_pad(substr($item['name'],0,28), 28, " ", STR_PAD_BOTH)."\x0D";
        $labelFormat .= $field03Format.str_pad(substr($item['description'],0,28), 28, " ", STR_PAD_BOTH)."\x0D";
        $labelFormat .= $field04Format.substr($item['item_number'],0,13)."\x0D";
        $labelFormat .= "Q".str_pad(number_format($item['quantity'],0), 4, "0", STR_PAD_LEFT)."E";
    }
    $labelFormat .= "\x01F"; //Form feed
    echo $labelFormat;
    fwrite($lp, $labelFormat);

    fclose($lp);
?>