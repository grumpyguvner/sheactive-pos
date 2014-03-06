<?php
    $lp = fopen("/dev/lp0", 'a');

    fwrite($lp, "\x01*");
    //sleep for 2 second to allow command to register
    usleep(2000000);
    $labelFormat = "\x02BPY\x0D"; //enable pound sign
    $labelFormat .= "\x02ySUK\x0D"; //select UK ISO 4 character set
    $labelReset = "\x02LD11";
    $field01Format = "191100200650010";
    $field02Format = "191100200540010";
    $field03Format = "192200200450010";
    $field04Format = "1e1203500000020";

    foreach($cart as $line=>$item)
    {
        $labelFormat .= $labelReset;
        $labelFormat .= $field01Format.str_pad(substr($item['comment'],0,22), 22, " ", STR_PAD_BOTH)."\x0D";
//        $labelFormat .= $field02Format.str_pad(substr($item['comment'],28,28), 28, " ", STR_PAD_BOTH)."\x0D";
        if ($item['id']<999999){
            $labelFormat .= $field03Format.str_pad(substr($item['reference'],3,13), 14, " ", STR_PAD_BOTH)."\x0D";
            $labelFormat .= $field04Format.str_pad(substr($item['id'],0,7),7,"0", STR_PAD_LEFT)."\x0D";
        }else{
            $labelFormat .= $field03Format.str_pad(substr("INVALID REF",0,13), 14, " ", STR_PAD_BOTH)."\x0D";
            $labelFormat .= $field04Format.str_pad(substr("0000000",0,7),7,"0", STR_PAD_LEFT)."\x0D";
        }

        $labelFormat .= "Q".str_pad(number_format($item['quantity'],0), 4, "0", STR_PAD_LEFT)."E";
    }
    $labelFormat .= "\x01F"; //Form feed
    echo $labelFormat;
    fwrite($lp, $labelFormat);

    fclose($lp);
?>