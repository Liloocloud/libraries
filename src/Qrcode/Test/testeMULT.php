<?php
/**
 * Sistema Gerador de QR Code
 * @copyright (c) Felipe Oliveira Lourenço, Central 7 web
 * @version 2.0.1
 * @create 13/02/2016
 */

/** Carrega a lib */
include "qrlib.php";

/**
 * QR Code [ Salvando a imagem na pasta ]
 */
// $path = "http://localhost/workcontrolrev/uploads/qrcodes/";
// QRcode::png("Conteúdo aqui", $path."nome-do-arquivo.png");
// echo "<img src='$path.nome-do-arquivo.png'>";


/**
 * QR Code [ envio de e-mail ]
 */
// $email 		= "contato@central7.com.br";
// $assunto 	= "Contato pelo site";
// $corpo 		= "Este é um e-mail de teste de QR Code";
// QRcode::png("mailto:" .$email."?subject=".$assunto."&body=".$corpo );


/**
 * QR Code [ Cartão de visitas ]
 */
// $nome 			= 'Felipe'; 
// $fonetrabalho 	= '(13) 988111850'; 
// $fonecasa 		= '(13) 982015093';
// $cel 			= '(13) 988111850';
// $email 			= 'contato@cetral7.com.br';
 
// $cartao  = 'BEGIN:VCARD'."\n"; 
// $cartao .= 'FN:'.$nome."\n"; 
// $cartao .= 'TEL;WORK;VOICE:'.$fonetrabalho."\n"; 
// $cartao .= 'TEL;HOME;VOICE:'.$fonecasa."\n"; 
// $cartao .= 'TEL;TYPE=cell:'.$cel."\n"; 
// $cartao .= 'EMAIL:'.$email."\n"; 
// $cartao .= 'END:VCARD'; 
// QRcode::png( $cartao );

/**
 * QR Code [ Acesso ao Skype ]
 */
$skypeUserName 	= 'rodrigoaramburu';
$content 		= 'skype:'.urlencode($skypeUserName).'?call'; 
QRcode::png( $content );