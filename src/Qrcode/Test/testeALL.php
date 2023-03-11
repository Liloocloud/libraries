<?php
require_once "./Autoloader.php";
require_once "./src/Qrcode/config/defines.php";

use Qrcode\Render;

$modelo = 'Felipe';
$id = '005';

$name         = $modelo; 			// Nome Completo
$sortName     = 'Doe;John'; 		// Apelido ou primeiro nome
$phone        = '13 999999999'; 		// Telefone Comercial
$phonePrivate = '13 999999999'; 		// Telefone Privado 
$phoneCell    = '(049)888-123-123'; 	// Celular
$orgName      = 'Liloo';  	// Nome da Empresa
$email        = 'felipe.game.studio@gmail.com'; 		// E-mail

// Dados de localização 
$addressLabel     = ''; 
$addressPobox     = ''; 
$addressExt       = ''; 

$addressStreet    = 'END'; 
$addressTown      = 'Santos'; 
$addressRegion    = 'SP'; 
$addressPostCode  = '11060-0002'; 
$addressCountry   = 'Brasil'; 


// Renderização de dados 
$codeContents  = 'BEGIN:VCARD'."\n"; 
$codeContents .= 'VERSION:2.1'."\n"; 
// $codeContents .= 'N:'.$sortName."\n"; 
$codeContents .= 'FN:'.$name."\n"; 
$codeContents .= 'ORG:'.$orgName."\n"; 

$codeContents .= 'TEL;WORK;VOICE:'.$phone."\n"; 
$codeContents .= 'TEL;HOME;VOICE:'.$phonePrivate."\n"; 
// $codeContents .= 'TEL;TYPE=cell:'.$phoneCell."\n"; 

$codeContents .= 'ADR;TYPE=work;'. 
    'LABEL="'.$addressLabel.'":' 
    .$addressPobox.';' 
    .$addressExt.';' 
    .$addressStreet.';' 
    .$addressTown.';' 
    .$addressPostCode.';' 
    .$addressCountry 
."\n"; 

$codeContents .= 'EMAIL:'.$email."\n"; 

$codeContents .= 'END:VCARD'; 

// Gerando arquivo de imagem 
Render::png($codeContents, 'upload/qr-'.$id.'.png', QR_ECLEVEL_L, 3); 

// Imprimindo QR Code no site
echo "<img class='img-auto' src='upload/qr-".$id.".png'/>";

