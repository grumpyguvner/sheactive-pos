<?php
    $lp = fopen("/dev/lp0", 'a');

  //Kick the drawer
    fwrite($lp, "\x1B\x70\x00\x19\xF0");

    fclose($lp);
?>