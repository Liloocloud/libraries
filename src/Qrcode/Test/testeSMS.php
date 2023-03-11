<?php
/**
 * GERANDO QR CODE PARA ENVIAR SMS
 */

    include "qrlib.php"; 


    // how to build raw content - QRCode to send SMS 
    $tempDir = "envio-sms-"; 
     
    // here our data 
    $phoneNo        = '(13) 98811-1850'; 
    $codeContents   = 'sms:'.$phoneNo; 
     
    // generating 
    QRcode::png($codeContents, $tempDir.'021.png', QR_ECLEVEL_L, 3); 
    
    // displaying
    echo "<h2>Enviando SMS</h2>";
    echo '<img src="'.$tempDir.'021.png" />'; 